<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Crear roles
        $superadminRole = Role::create([
            'name' => 'Superadmin',
            'description' => 'Administrador del sistema con acceso total',
        ]);

        Role::create([
            'name' => 'Admin',
            'description' => 'Administrador de entidad',
        ]);

        Role::create([
            'name' => 'Contador',
            'description' => 'Usuario contador con acceso a facturación',
        ]);

        Role::create([
            'name' => 'Auditor',
            'description' => 'Usuario auditor con acceso de lectura',
        ]);

        Role::create([
            'name' => 'Operador',
            'description' => 'Usuario operador de importación',
        ]);

        // Crear usuario Superadmin
        $superadmin = User::create([
            'name' => 'Soporte SGDEA',
            'email' => 'soporte@maritimosarboleda.com',
            'password' => Hash::make('Soporte@2024'),
            'status' => 'activo',
        ]);

        // Asignar rol Superadmin al usuario
        $superadmin->roles()->attach($superadminRole);
    }
}

