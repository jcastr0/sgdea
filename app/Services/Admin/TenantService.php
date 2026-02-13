<?php

namespace App\Services\Admin;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Role;
use App\Models\Permission;
use App\Models\ThemeConfiguration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

/**
 * ============================================================================
 * SERVICE: TenantService
 * ============================================================================
 *
 * Servicio para la gestión de tenants.
 * Centraliza la lógica de creación, actualización y eliminación de tenants.
 *
 * @author SGDEA Team
 * ============================================================================
 */
class TenantService
{
    /**
     * Crear un nuevo tenant con toda su configuración
     *
     * @param array $data Datos del tenant
     * @return array Resultado con tenant creado y credenciales
     * @throws \Exception
     */
    public function createTenant(array $data): array
    {
        $result = [
            'success' => false,
            'tenant' => null,
            'user' => null,
            'password' => null,
            'errors' => [],
            'steps' => [],
        ];

        try {
            DB::beginTransaction();

            // =====================
            // PASO 1: Crear Tenant
            // =====================
            $result['steps'][] = ['step' => 1, 'action' => 'Creando tenant...'];

            $tenantData = [
                'name' => $data['company_name'],
                'slug' => $data['slug'] ?? Str::slug($data['company_name']),
                'domain' => $data['domain'],
                'status' => $data['status'] ?? 'active',
                'created_by' => $data['created_by'] ?? auth()->id(),
                // Colores del tema - guardados directamente en la tabla tenants
                'primary_color' => $data['color_primary'] ?? '#2563eb',
                'secondary_color' => $data['color_secondary'] ?? '#0f172a',
            ];

            // Agregar campos opcionales si existen en la tabla
            if (Schema::hasColumn('tenants', 'plan')) {
                $tenantData['plan'] = $data['plan'] ?? 'professional';
            }
            if (Schema::hasColumn('tenants', 'max_users')) {
                $tenantData['max_users'] = $data['max_users'] ?? 0;
            }
            if (Schema::hasColumn('tenants', 'max_storage')) {
                $tenantData['max_storage'] = $data['max_storage'] ?? 0;
            }

            $tenant = Tenant::create($tenantData);
            $result['steps'][] = ['step' => 1, 'action' => 'Tenant creado con colores personalizados', 'id' => $tenant->id];

            // =====================
            // PASO 2: Theme (legacy - mantener compatibilidad)
            // =====================
            // NOTA: Los colores principales ya están en la tabla tenants.
            // Este paso se mantiene por compatibilidad con el sistema legacy de themes.
            $result['steps'][] = ['step' => 2, 'action' => 'Configurando tema (colores guardados en tenant)...'];

            // Solo crear ThemeConfiguration si la tabla existe y se necesita para compatibilidad
            if (Schema::hasTable('theme_configurations')) {
                $themeData = [
                    'tenant_id' => $tenant->id,
                    'color_primary' => $data['color_primary'] ?? '#2563eb',
                    'color_secondary' => $data['color_secondary'] ?? '#0f172a',
                    'color_accent' => $data['color_accent'] ?? '#10b981',
                    'color_error' => '#ef4444',
                    'color_success' => '#10b981',
                    'color_warning' => '#f59e0b',
                    'color_bg_light' => '#f8fafc',
                    'color_bg_dark' => '#0f172a',
                    'color_text_primary' => '#1f2937',
                    'color_text_secondary' => '#6b7280',
                    'color_border' => '#e5e7eb',
                    'dark_mode_enabled' => $data['dark_mode_enabled'] ?? true,
                ];

                $theme = ThemeConfiguration::create($themeData);
                $result['steps'][] = ['step' => 2, 'action' => 'Tema legacy creado', 'id' => $theme->id];
            } else {
                $result['steps'][] = ['step' => 2, 'action' => 'Colores guardados en tenant (sin theme_configurations)'];
            }

            // =====================
            // PASO 3: Crear Rol Admin
            // =====================
            $result['steps'][] = ['step' => 3, 'action' => 'Creando rol administrador...'];

            $adminRole = Role::firstOrCreate(
                ['tenant_id' => $tenant->id, 'slug' => 'administrador'],
                [
                    'name' => 'Administrador',
                    'description' => 'Administrador del tenant con acceso total',
                    'is_system' => true,
                    'priority' => 100,
                ]
            );

            // Asignar TODOS los permisos al rol administrador (excepto los de admin global)
            $allPermissions = Permission::whereNotIn('resource', ['admin'])->pluck('id');
            $adminRole->permissions()->syncWithoutDetaching($allPermissions);

            $result['steps'][] = ['step' => 3, 'action' => 'Rol creado con ' . count($allPermissions) . ' permisos', 'id' => $adminRole->id];

            // =====================
            // PASO 4: Crear Usuario Admin
            // =====================
            $result['steps'][] = ['step' => 4, 'action' => 'Creando usuario administrador...'];

            // Generar password si no se proporcionó
            $plainPassword = $data['admin_password'] ?? Str::random(12);

            $userData = [
                'tenant_id' => $tenant->id,
                'role_id' => $adminRole->id,
                'name' => $data['admin_name'],
                'email' => $data['admin_email'],
                'password' => Hash::make($plainPassword),
                'status' => 'active',
                'email_verified_at' => now(),
            ];

            $user = User::create($userData);
            $result['steps'][] = ['step' => 4, 'action' => 'Usuario creado', 'id' => $user->id, 'email' => $user->email];

            // =====================
            // PASO 5: Commit
            // =====================
            DB::commit();

            $result['success'] = true;
            $result['tenant'] = $tenant;
            $result['user'] = $user;
            $result['password'] = $plainPassword;
            $result['steps'][] = ['step' => 5, 'action' => '✅ Transacción completada exitosamente'];

        } catch (\Exception $e) {
            DB::rollBack();
            $result['success'] = false;
            $result['errors'][] = $e->getMessage();
            $result['steps'][] = ['step' => 'ERROR', 'action' => '❌ Rollback: ' . $e->getMessage()];
        }

        return $result;
    }

    /**
     * Validar datos antes de crear un tenant
     *
     * @param array $data Datos a validar
     * @return array Errores encontrados
     */
    public function validateTenantData(array $data): array
    {
        $errors = [];

        // Validar campos requeridos
        if (empty($data['company_name'])) {
            $errors[] = 'El nombre de la empresa es obligatorio';
        }

        if (empty($data['domain'])) {
            $errors[] = 'El dominio es obligatorio';
        }

        if (empty($data['admin_name'])) {
            $errors[] = 'El nombre del administrador es obligatorio';
        }

        if (empty($data['admin_email'])) {
            $errors[] = 'El email del administrador es obligatorio';
        } elseif (!filter_var($data['admin_email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'El email del administrador no es válido';
        }

        // Validar unicidad
        if (!empty($data['company_name'])) {
            $exists = Tenant::where('name', $data['company_name'])->exists();
            if ($exists) {
                $errors[] = 'Ya existe un tenant con ese nombre';
            }
        }

        if (!empty($data['domain'])) {
            $exists = Tenant::where('domain', $data['domain'])->exists();
            if ($exists) {
                $errors[] = 'Ya existe un tenant con ese dominio';
            }
        }

        if (!empty($data['admin_email'])) {
            $exists = User::where('email', $data['admin_email'])->exists();
            if ($exists) {
                $errors[] = 'Ya existe un usuario con ese email';
            }
        }

        // Validar slug
        $slug = $data['slug'] ?? Str::slug($data['company_name'] ?? '');
        if (!empty($slug)) {
            $exists = Tenant::where('slug', $slug)->exists();
            if ($exists) {
                $errors[] = 'Ya existe un tenant con ese slug';
            }
        }

        return $errors;
    }

    /**
     * Eliminar un tenant y todos sus datos relacionados
     *
     * @param int $tenantId ID del tenant
     * @return array Resultado de la operación
     */
    public function deleteTenant(int $tenantId): array
    {
        $result = [
            'success' => false,
            'errors' => [],
            'deleted' => [],
        ];

        try {
            $tenant = Tenant::findOrFail($tenantId);

            DB::beginTransaction();

            // Eliminar usuarios
            $usersDeleted = User::where('tenant_id', $tenantId)->delete();
            $result['deleted']['users'] = $usersDeleted;

            // Eliminar roles
            $rolesDeleted = Role::where('tenant_id', $tenantId)->delete();
            $result['deleted']['roles'] = $rolesDeleted;

            // Eliminar tema
            $themeDeleted = ThemeConfiguration::where('tenant_id', $tenantId)->delete();
            $result['deleted']['theme'] = $themeDeleted;

            // Eliminar facturas
            $facturasDeleted = DB::table('facturas')->where('tenant_id', $tenantId)->delete();
            $result['deleted']['facturas'] = $facturasDeleted;

            // Eliminar terceros
            $tercerosDeleted = DB::table('terceros')->where('tenant_id', $tenantId)->delete();
            $result['deleted']['terceros'] = $tercerosDeleted;

            // Eliminar tenant
            $tenant->delete();
            $result['deleted']['tenant'] = 1;

            DB::commit();
            $result['success'] = true;

        } catch (\Exception $e) {
            DB::rollBack();
            $result['errors'][] = $e->getMessage();
        }

        return $result;
    }

    /**
     * Cambiar estado de un tenant
     *
     * @param int $tenantId ID del tenant
     * @param string|null $newStatus Nuevo estado (null para toggle)
     * @return array Resultado
     */
    public function toggleStatus(int $tenantId, ?string $newStatus = null): array
    {
        $tenant = Tenant::findOrFail($tenantId);

        if ($newStatus === null) {
            $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';
        }

        $tenant->update(['status' => $newStatus]);

        return [
            'success' => true,
            'tenant_id' => $tenantId,
            'old_status' => $tenant->getOriginal('status'),
            'new_status' => $newStatus,
        ];
    }
}

