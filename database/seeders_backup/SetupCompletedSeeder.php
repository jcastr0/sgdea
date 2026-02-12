<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Permission;
use App\Models\SetupCheckpoint;
use App\Models\SystemUser;
use App\Models\Tenant;
use App\Models\ThemeConfiguration;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\File;

class SetupCompletedSeeder extends Seeder
{
    /**
     * Ejecuta el seeder para simular un setup completo.
     * Crea todos los datos necesarios para que el sistema funcione.
     */
    public function run(): void
    {
        $this->command->info('🚀 Iniciando configuración completa del sistema...');

        // 1. Crear Superadmin Global (system_users) - No requiere tenant
        $superadminGlobal = $this->createSystemSuperadmin();
        $this->command->info('✅ Superadmin Global creado');

        // 2. Crear Tenant Principal (empresa demo)
        $tenant = $this->createMainTenant();
        $this->command->info('✅ Tenant principal creado');

        // 3. Crear roles del sistema (requiere tenant)
        $this->createRoles($tenant);
        $this->command->info('✅ Roles creados');

        // 4. Crear permisos básicos
        $this->createPermissions($tenant);
        $this->command->info('✅ Permisos creados');

        // 5. Crear Tema para el Tenant
        $this->createThemeForTenant($tenant);
        $this->command->info('✅ Tema configurado');

        // 6. Crear Usuario Superadmin del Tenant
        $tenantAdmin = $this->createTenantSuperadmin($tenant);
        $this->command->info('✅ Superadmin del Tenant creado');

        // 7. Completar todos los checkpoints del Setup
        $this->completeSetupCheckpoints();
        $this->command->info('✅ Checkpoints del setup completados');

        // 8. Crear archivo de marca de setup completado
        $this->markSetupAsCompleted();
        $this->command->info('✅ Setup marcado como completado');

        $this->command->newLine();
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->info('🎉 ¡Sistema configurado exitosamente!');
        $this->command->info('═══════════════════════════════════════════════════════════');
        $this->command->newLine();
        $this->command->info('📧 CREDENCIALES DE ACCESO:');
        $this->command->info('');
        $this->command->info('   👤 Superadmin Global (gestión de tenants):');
        $this->command->info('      Email: admin@sgdea.local');
        $this->command->info('      Password: Admin@2024');
        $this->command->info('');
        $this->command->info('   👤 Admin del Tenant (operaciones):');
        $this->command->info('      Email: admin@demo.sgdea.local');
        $this->command->info('      Password: Admin@2024');
        $this->command->info('');
        $this->command->info('   🌐 URL: http://localhost:8080');
        $this->command->info('═══════════════════════════════════════════════════════════');
    }

    /**
     * Crear roles del sistema
     */
    private function createRoles(Tenant $tenant): void
    {
        $roles = [
            [
                'name' => 'Superadmin',
                'slug' => 'superadmin',
                'description' => 'Administrador del sistema con acceso total',
                'is_base' => true,
            ],
            [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrador de entidad/tenant',
                'is_base' => true,
            ],
            [
                'name' => 'Contador',
                'slug' => 'contador',
                'description' => 'Usuario contador con acceso a facturación',
                'is_base' => true,
            ],
            [
                'name' => 'Auditor',
                'slug' => 'auditor',
                'description' => 'Usuario auditor con acceso de lectura',
                'is_base' => true,
            ],
            [
                'name' => 'Operador',
                'slug' => 'operador',
                'description' => 'Usuario operador de importación',
                'is_base' => true,
            ],
        ];

        foreach ($roles as $role) {
            Role::updateOrCreate(
                ['name' => $role['name']],
                array_merge($role, ['tenant_id' => $tenant->id])
            );
        }
    }

    /**
     * Crear permisos básicos
     */
    private function createPermissions(Tenant $tenant): void
    {
        $permissions = [
            // Terceros
            ['name' => 'terceros.view', 'slug' => 'terceros-view', 'description' => 'Ver terceros', 'resource' => 'terceros'],
            ['name' => 'terceros.create', 'slug' => 'terceros-create', 'description' => 'Crear terceros', 'resource' => 'terceros'],
            ['name' => 'terceros.edit', 'slug' => 'terceros-edit', 'description' => 'Editar terceros', 'resource' => 'terceros'],
            ['name' => 'terceros.delete', 'slug' => 'terceros-delete', 'description' => 'Eliminar terceros', 'resource' => 'terceros'],
            // Facturas
            ['name' => 'facturas.view', 'slug' => 'facturas-view', 'description' => 'Ver facturas', 'resource' => 'facturas'],
            ['name' => 'facturas.create', 'slug' => 'facturas-create', 'description' => 'Crear facturas', 'resource' => 'facturas'],
            ['name' => 'facturas.edit', 'slug' => 'facturas-edit', 'description' => 'Editar facturas', 'resource' => 'facturas'],
            ['name' => 'facturas.delete', 'slug' => 'facturas-delete', 'description' => 'Eliminar facturas', 'resource' => 'facturas'],
            // Importaciones
            ['name' => 'importaciones.view', 'slug' => 'importaciones-view', 'description' => 'Ver importaciones', 'resource' => 'importaciones'],
            ['name' => 'importaciones.execute', 'slug' => 'importaciones-execute', 'description' => 'Ejecutar importaciones', 'resource' => 'importaciones'],
            // Usuarios
            ['name' => 'usuarios.view', 'slug' => 'usuarios-view', 'description' => 'Ver usuarios', 'resource' => 'usuarios'],
            ['name' => 'usuarios.manage', 'slug' => 'usuarios-manage', 'description' => 'Gestionar usuarios', 'resource' => 'usuarios'],
            ['name' => 'usuarios.approve', 'slug' => 'usuarios-approve', 'description' => 'Aprobar usuarios', 'resource' => 'usuarios'],
            // Auditoría
            ['name' => 'auditoria.view', 'slug' => 'auditoria-view', 'description' => 'Ver auditoría', 'resource' => 'auditoria'],
            ['name' => 'auditoria.export', 'slug' => 'auditoria-export', 'description' => 'Exportar auditoría', 'resource' => 'auditoria'],
            // Configuración
            ['name' => 'configuracion.view', 'slug' => 'configuracion-view', 'description' => 'Ver configuración', 'resource' => 'configuracion'],
            ['name' => 'configuracion.edit', 'slug' => 'configuracion-edit', 'description' => 'Editar configuración', 'resource' => 'configuracion'],
        ];

        foreach ($permissions as $perm) {
            Permission::updateOrCreate(
                ['name' => $perm['name']],
                $perm
            );
        }

        // Asignar permisos a roles
        $this->assignPermissionsToRoles();
    }

    /**
     * Asignar permisos a roles
     */
    private function assignPermissionsToRoles(): void
    {
        $superadmin = Role::where('name', 'Superadmin')->first();
        $admin = Role::where('name', 'Admin')->first();
        $contador = Role::where('name', 'Contador')->first();
        $auditor = Role::where('name', 'Auditor')->first();
        $operador = Role::where('name', 'Operador')->first();

        // Superadmin tiene todos los permisos
        if ($superadmin) {
            $superadmin->permissions()->sync(Permission::pluck('id'));
        }

        // Admin tiene casi todos (excepto configuración global)
        if ($admin) {
            $adminPerms = Permission::whereNotIn('name', ['configuracion.edit'])->pluck('id');
            $admin->permissions()->sync($adminPerms);
        }

        // Contador: terceros, facturas, importaciones (ver)
        if ($contador) {
            $contadorPerms = Permission::whereIn('name', [
                'terceros.view', 'terceros.create', 'terceros.edit',
                'facturas.view', 'facturas.create', 'facturas.edit',
                'importaciones.view', 'importaciones.execute',
            ])->pluck('id');
            $contador->permissions()->sync($contadorPerms);
        }

        // Auditor: solo ver
        if ($auditor) {
            $auditorPerms = Permission::whereIn('name', [
                'terceros.view', 'facturas.view', 'importaciones.view',
                'auditoria.view', 'auditoria.export',
            ])->pluck('id');
            $auditor->permissions()->sync($auditorPerms);
        }

        // Operador: importaciones
        if ($operador) {
            $operadorPerms = Permission::whereIn('name', [
                'terceros.view', 'facturas.view',
                'importaciones.view', 'importaciones.execute',
            ])->pluck('id');
            $operador->permissions()->sync($operadorPerms);
        }
    }

    /**
     * Crear Superadmin Global (en system_users)
     */
    private function createSystemSuperadmin(): SystemUser
    {
        return SystemUser::updateOrCreate(
            ['email' => 'admin@sgdea.local'],
            [
                'name' => 'Administrador SGDEA',
                'password' => Hash::make('Admin@2024'),
                'is_superadmin' => true,
                'email_verified_at' => now(),
            ]
        );
    }

    /**
     * Crear Tenant Principal
     */
    private function createMainTenant(): Tenant
    {
        return Tenant::updateOrCreate(
            ['slug' => 'demo'],
            [
                'name' => 'Empresa Demo',
                'slug' => 'demo',
                'domain' => 'demo.sgdea.local',
                'database_name' => 'gestion_fiscal',
                'status' => 'active',
                'logo_path' => '/images/logo/logo_sgdea.png',
            ]
        );
    }

    /**
     * Crear Tema para el Tenant
     */
    private function createThemeForTenant(Tenant $tenant): ThemeConfiguration
    {
        return ThemeConfiguration::updateOrCreate(
            ['tenant_id' => $tenant->id],
            [
                'color_primary' => '#3B82F6',
                'color_secondary' => '#1D4ED8',
                'color_error' => '#DC2626',
                'color_success' => '#10B981',
                'color_warning' => '#F59E0B',
                'color_bg_light' => '#F8FAFC',
                'color_bg_dark' => '#1E293B',
                'color_text_primary' => '#1E293B',
                'color_text_secondary' => '#64748B',
                'color_border' => '#E2E8F0',
                'font_header' => 'Inter, Roboto, sans-serif',
                'font_body' => 'Inter, Roboto, sans-serif',
                'font_mono' => 'Courier New, monospace',
                'size_base' => '16px',
                'size_h1' => '2.25rem',
                'size_h2' => '1.75rem',
                'size_h3' => '1.5rem',
                'size_small' => '0.875rem',
                'spacing_sm' => '8px',
                'spacing_md' => '16px',
                'spacing_lg' => '24px',
                'radius_sm' => '6px',
                'radius_md' => '8px',
                'shadow_enabled' => true,
                'shadow_intensity' => 'light',
                'gradient_enabled' => false,
                'logo_height' => 40,
                'show_company_name' => true,
            ]
        );
    }

    /**
     * Crear Superadmin del Tenant (usuario normal con rol Superadmin)
     */
    private function createTenantSuperadmin(Tenant $tenant): User
    {
        $superadminRole = Role::where('name', 'Superadmin')->first();

        $user = User::updateOrCreate(
            ['email' => 'admin@demo.sgdea.local'],
            [
                'name' => 'Administrador Demo',
                'password' => Hash::make('Admin@2024'),
                'tenant_id' => $tenant->id,
                'status' => 'active',
                'email_verified_at' => now(),
            ]
        );

        // Asignar rol Superadmin
        if ($superadminRole) {
            $user->roles()->syncWithoutDetaching([$superadminRole->id]);
        }

        return $user;
    }

    /**
     * Completar todos los checkpoints del Setup
     */
    private function completeSetupCheckpoints(): void
    {
        $checkpoints = [
            [
                'step_key' => 'setup_step_superadmin_created',
                'step_name' => 'Crear Superadmin Global',
                'step_order' => 1,
                'phase' => 'FASE_1',
                'component' => 'superadmin',
                'status' => 'completed',
                'completion_date' => now(),
            ],
            [
                'step_key' => 'setup_step_mysql_connected',
                'step_name' => 'Conectar MySQL',
                'step_order' => 2,
                'phase' => 'FASE_1',
                'component' => 'database',
                'status' => 'completed',
                'completion_date' => now(),
            ],
            [
                'step_key' => 'setup_step_first_tenant_and_theme',
                'step_name' => 'Crear Primer Tenant y Tema',
                'step_order' => 3,
                'phase' => 'FASE_1',
                'component' => 'tenant_theme',
                'status' => 'completed',
                'completion_date' => now(),
            ],
            [
                'step_key' => 'setup_step_email_configured',
                'step_name' => 'Configurar Email (Opcional)',
                'step_order' => 4,
                'phase' => 'FASE_1',
                'component' => 'email',
                'status' => 'completed',
                'completion_date' => now(),
            ],
            [
                'step_key' => 'setup_step_ldap_configured',
                'step_name' => 'Configurar LDAP (Opcional)',
                'step_order' => 5,
                'phase' => 'FASE_1',
                'component' => 'ldap',
                'status' => 'completed',
                'completion_date' => now(),
            ],
            [
                'step_key' => 'setup_step_verification_passed',
                'step_name' => 'Verificación Final',
                'step_order' => 6,
                'phase' => 'FASE_1',
                'component' => 'verification',
                'status' => 'completed',
                'completion_date' => now(),
            ],
        ];

        foreach ($checkpoints as $checkpoint) {
            SetupCheckpoint::updateOrCreate(
                ['step_key' => $checkpoint['step_key']],
                $checkpoint
            );
        }
    }

    /**
     * Marcar el setup como completado creando archivo de marca
     */
    private function markSetupAsCompleted(): void
    {
        $marker = storage_path('.setup_completed');
        $content = json_encode([
            'completed_at' => now()->toIso8601String(),
            'version' => '1.0.0',
            'seeder' => 'SetupCompletedSeeder',
        ], JSON_PRETTY_PRINT);

        File::put($marker, $content);
    }
}
