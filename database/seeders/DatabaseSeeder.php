<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder
 *
 * Seeder principal que orquesta la ejecución de todos los seeders.
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

        // 1. Permisos del sistema (globales, no dependen de tenant)
        $this->call(PermissionSeeder::class);

        // 2. Sistema: Admin global y tenant de demostración
        $this->call(SystemSeeder::class);

        // 3. Roles base (dependen de tenant y permisos)
        $this->call(RoleSeeder::class);

        // 4. Usuarios de demostración (dependen de tenant y roles)
        $this->call(UserSeeder::class);

        // 5. Checkpoints del setup
        $this->call(SetupSeeder::class);

        $this->command->newLine();
        $this->command->info('✅ Seeders completados exitosamente');
    }
}

