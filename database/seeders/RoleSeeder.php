<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use App\Models\Tenant;
use Illuminate\Database\Seeder;

/**
 * RoleSeeder
 *
 * Crea los roles base del sistema y asigna permisos.
 *
 * Roles por defecto:
 * - Administrador: Todos los permisos
 * - Supervisor: Permisos de gestión sin eliminar
 * - Operador: Permisos básicos de operación
 * - Consultor: Solo lectura
 */
class RoleSeeder extends Seeder
{
    /**
     * Definición de roles y sus permisos
     */
    private array $roles = [
        [
            'name' => 'Administrador',
            'slug' => 'administrador',
            'description' => 'Acceso completo a todas las funcionalidades del sistema',
            'is_system' => true,
            'is_default' => false,
            'priority' => 100,
            'permissions' => '*', // Todos los permisos
        ],
        [
            'name' => 'Supervisor',
            'slug' => 'supervisor',
            'description' => 'Puede gestionar facturas, terceros e importaciones. No puede eliminar ni configurar.',
            'is_system' => true,
            'is_default' => false,
            'priority' => 75,
            'permissions' => [
                'dashboard.view',
                'dashboard.export',
                'facturas.view',
                'facturas.create',
                'facturas.edit',
                'facturas.export',
                'terceros.view',
                'terceros.create',
                'terceros.edit',
                'terceros.merge',
                'importaciones.view',
                'importaciones.execute',
                'usuarios.view',
                'auditoria.view',
            ],
        ],
        [
            'name' => 'Operador',
            'slug' => 'operador',
            'description' => 'Puede ver y crear facturas e importar archivos. Sin acceso a terceros ni configuración.',
            'is_system' => true,
            'is_default' => true, // Rol por defecto para nuevos usuarios
            'priority' => 50,
            'permissions' => [
                'dashboard.view',
                'facturas.view',
                'facturas.create',
                'terceros.view',
                'importaciones.view',
                'importaciones.execute',
            ],
        ],
        [
            'name' => 'Consultor',
            'slug' => 'consultor',
            'description' => 'Solo puede ver información. Sin permisos de creación o modificación.',
            'is_system' => true,
            'is_default' => false,
            'priority' => 25,
            'permissions' => [
                'dashboard.view',
                'facturas.view',
                'terceros.view',
                'importaciones.view',
            ],
        ],
    ];

    public function run(): void
    {
        $this->command->info('👥 Creando roles del sistema...');

        // Obtener el tenant demo
        $tenant = Tenant::where('slug', 'demo')->first();

        if (!$tenant) {
            $this->command->error('   ❌ No se encontró el tenant demo. Ejecute SystemSeeder primero.');
            return;
        }

        // Obtener todos los permisos
        $allPermissions = Permission::all();

        foreach ($this->roles as $roleData) {
            // Crear o actualizar el rol
            $role = Role::updateOrCreate(
                [
                    'tenant_id' => $tenant->id,
                    'slug' => $roleData['slug'],
                ],
                [
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                    'is_system' => $roleData['is_system'],
                    'is_default' => $roleData['is_default'],
                    'priority' => $roleData['priority'],
                ]
            );

            // Asignar permisos
            if ($roleData['permissions'] === '*') {
                // Todos los permisos
                $role->permissions()->sync($allPermissions->pluck('id'));
                $this->command->info("   ✅ Rol '{$role->name}' creado con TODOS los permisos");
            } else {
                // Permisos específicos
                $permissionIds = Permission::whereIn('name', $roleData['permissions'])
                    ->pluck('id');
                $role->permissions()->sync($permissionIds);
                $this->command->info("   ✅ Rol '{$role->name}' creado con " . count($roleData['permissions']) . " permisos");
            }
        }
    }
}

