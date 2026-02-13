<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder
 *
 * Seeder principal que orquesta la ejecución de todos los seeders.
 *
 * ORDEN DE EJECUCIÓN:
 * 1. SystemUserSeeder - Usuario SYSTEM (ID=1) para auditoría [OBLIGATORIO]
 * 2. PermissionSeeder - Permisos del sistema
 * 3. RoleSeeder - Roles globales con permisos
 *
 * EN DESARROLLO (APP_ENV=local):
 * 4. SystemSeeder - Tenant demo + Tema de ejemplo
 * 5. UserSeeder - Usuarios de demostración
 * 6. SetupSeeder - Checkpoints del setup
 *
 * EN PRODUCCIÓN (APP_ENV=production):
 * - NO se crean tenants de ejemplo
 * - NO se crean usuarios de ejemplo
 * - Solo se crean roles, permisos y usuario SYSTEM
 * - Los tenants y usuarios se crean con los comandos artisan
 *
 * Ejecutar con: php artisan db:seed
 */
class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $isProduction = app()->isProduction();
        $environment = app()->environment();

        $this->command->info('🚀 Iniciando seeders del sistema SGDEA...');
        $this->command->info("   Entorno detectado: {$environment}");
        $this->command->newLine();

        // ============================================
        // SEEDERS OBLIGATORIOS (SIEMPRE SE EJECUTAN)
        // ============================================

        // 1. USUARIO SYSTEM - OBLIGATORIO Y PRIMERO
        // Este usuario es requerido por el sistema de auditoría.
        // Debe crearse ANTES de cualquier otra entidad que use Auditable.
        $this->call(SystemUserSeeder::class);

        // 2. Permisos del sistema (globales, no dependen de tenant)
        $this->call(PermissionSeeder::class);

        // 3. Roles globales (superadmin_global - sin tenant)
        $this->call(RoleSeeder::class);

        // ============================================
        // SEEDERS SOLO EN DESARROLLO
        // ============================================
        if (!$isProduction) {
            $this->command->newLine();
            $this->command->warn('📦 Cargando datos de demostración (entorno de desarrollo)...');
            $this->command->newLine();

            // 4. Sistema: Tenant demo + Tema de ejemplo
            $this->call(SystemSeeder::class);

            // 5. Usuarios de demostración (dependen de tenant y roles)
            $this->call(UserSeeder::class);

            // 6. Checkpoints del setup
            $this->call(SetupSeeder::class);
        } else {
            $this->command->newLine();
            $this->command->info('🔒 Modo PRODUCCIÓN: No se crean datos de ejemplo');
            $this->command->info('   • Para crear tenants: php artisan tenant:create');
            $this->command->info('   • Para crear superadmin: php artisan app:create-superadmin');
            $this->command->newLine();
        }

        $this->command->newLine();
        $this->command->info('✅ Seeders completados exitosamente');

        if ($isProduction) {
            $this->command->newLine();
            $this->command->warn('════════════════════════════════════════════════════════');
            $this->command->warn('  PRÓXIMOS PASOS EN PRODUCCIÓN:');
            $this->command->warn('════════════════════════════════════════════════════════');
            $this->command->info('  1. Crear superadmin global:');
            $this->command->info('     php artisan app:create-superadmin --global');
            $this->command->newLine();
            $this->command->info('  2. Acceder al sistema y crear tenants desde el panel');
            $this->command->info('     O usar: php artisan tenant:create');
            $this->command->warn('════════════════════════════════════════════════════════');
        }
    }
}

