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
 * 3. SystemSeeder - Superadmin global + Tenant demo + Tema
 * 4. RoleSeeder - Roles con permisos
 * 5. UserSeeder - Usuarios de demostración
 * 6. SetupSeeder - Checkpoints del setup
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
        $this->command->info('🚀 Iniciando seeders del sistema SGDEA...');
        $this->command->newLine();

        // ============================================
        // 1. USUARIO SYSTEM - OBLIGATORIO Y PRIMERO
        // ============================================
        // Este usuario es requerido por el sistema de auditoría.
        // Debe crearse ANTES de cualquier otra entidad que use Auditable.
        $this->call(SystemUserSeeder::class);

        // 2. Permisos del sistema (globales, no dependen de tenant)
        $this->call(PermissionSeeder::class);

        // 3. Sistema: Admin global y tenant de demostración
        $this->call(SystemSeeder::class);

        // 4. Roles base (dependen de tenant y permisos)
        $this->call(RoleSeeder::class);

        // 5. Usuarios de demostración (dependen de tenant y roles)
        $this->call(UserSeeder::class);

        // 6. Checkpoints del setup
        $this->call(SetupSeeder::class);

        $this->command->newLine();
        $this->command->info('✅ Seeders completados exitosamente');
    }
}

