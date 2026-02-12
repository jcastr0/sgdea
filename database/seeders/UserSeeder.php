<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\Tenant;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * UserSeeder
 *
 * Crea usuarios de demostración para el sistema.
 *
 * Usuarios creados:
 * - admin@demo.sgdea.local (Administrador)
 * - supervisor@demo.sgdea.local (Supervisor)
 * - operador@demo.sgdea.local (Operador)
 * - consultor@demo.sgdea.local (Consultor)
 */
class UserSeeder extends Seeder
{
    /**
     * Definición de usuarios de demostración
     */
    private array $users = [
        [
            'name' => 'Admin Demo',
            'email' => 'admin@demo.sgdea.local',
            'password' => 'Admin123!',
            'role_slug' => 'administrador',
            'status' => 'active',
            'department' => 'Administración',
        ],
        [
            'name' => 'Supervisor Demo',
            'email' => 'supervisor@demo.sgdea.local',
            'password' => 'Supervisor123!',
            'role_slug' => 'supervisor',
            'status' => 'active',
            'department' => 'Operaciones',
        ],
        [
            'name' => 'Operador Demo',
            'email' => 'operador@demo.sgdea.local',
            'password' => 'Operador123!',
            'role_slug' => 'operador',
            'status' => 'active',
            'department' => 'Facturación',
        ],
        [
            'name' => 'Consultor Demo',
            'email' => 'consultor@demo.sgdea.local',
            'password' => 'Consultor123!',
            'role_slug' => 'consultor',
            'status' => 'active',
            'department' => 'Auditoría',
        ],
    ];

    public function run(): void
    {
        $this->command->info('👤 Creando usuarios de demostración...');

        // Obtener el tenant demo
        $tenant = Tenant::where('slug', 'demo')->first();

        if (!$tenant) {
            $this->command->error('   ❌ No se encontró el tenant demo. Ejecute SystemSeeder primero.');
            return;
        }

        // Obtener roles
        $roles = Role::where('tenant_id', $tenant->id)->get()->keyBy('slug');

        foreach ($this->users as $userData) {
            $role = $roles[$userData['role_slug']] ?? null;

            $user = User::updateOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make($userData['password']),
                    'tenant_id' => $tenant->id,
                    'role_id' => $role?->id,
                    'status' => $userData['status'],
                    'department' => $userData['department'],
                    'email_verified_at' => now(),
                    'approved_at' => now(),
                ]
            );

            // Sincronizar con tabla pivote user_role
            if ($role) {
                $user->roles()->syncWithoutDetaching([$role->id]);
            }

            $this->command->info("   ✅ Usuario '{$user->email}' creado con rol '{$userData['role_slug']}'");
        }

        $this->command->newLine();
        $this->command->warn('   📝 Credenciales de acceso:');
        $this->command->line('      admin@demo.sgdea.local / Admin123!');
        $this->command->line('      supervisor@demo.sgdea.local / Supervisor123!');
        $this->command->line('      operador@demo.sgdea.local / Operador123!');
        $this->command->line('      consultor@demo.sgdea.local / Consultor123!');
    }
}

