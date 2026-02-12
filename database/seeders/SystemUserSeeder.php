<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * SystemUserSeeder
 *
 * Crea el usuario SYSTEM (ID=1) que es OBLIGATORIO para el sistema de auditoría.
 *
 * IMPORTANTE: Este seeder DEBE ejecutarse PRIMERO, antes de cualquier otro seeder,
 * ya que el trait Auditable requiere que exista este usuario para registrar acciones.
 *
 * Características del usuario SYSTEM:
 * - ID fijo = 1 (User::SYSTEM_ID)
 * - NO puede iniciar sesión (status=inactive)
 * - Se usa para registrar acciones sin usuario autenticado:
 *   - Logins fallidos
 *   - Acciones de consola
 *   - Procesos automatizados
 *   - Jobs en cola
 */
class SystemUserSeeder extends Seeder
{
    public function run(): void
    {
        $this->command->info('🤖 Creando usuario SYSTEM para auditoría...');

        // Verificar si ya existe
        $existingSystem = User::find(User::SYSTEM_ID);

        if ($existingSystem) {
            $this->command->info('   ⏭️  Usuario SYSTEM ya existe (ID=' . User::SYSTEM_ID . ')');
            return;
        }

        // Crear usuario SYSTEM usando DB directamente para evitar el trait Auditable
        // No usamos el modelo User porque activaría la auditoría
        \DB::table('users')->insert([
            'id' => User::SYSTEM_ID,
            'name' => 'SYSTEM',
            'email' => User::SYSTEM_EMAIL,
            'password' => Hash::make(Str::random(64)), // Contraseña imposible de adivinar
            'status' => 'inactive', // NO puede iniciar sesión
            'email_verified_at' => now(),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $this->command->info('   ✅ Usuario SYSTEM creado (ID=' . User::SYSTEM_ID . ')');
        $this->command->warn('   ⚠️  Este usuario NO puede iniciar sesión - solo para auditoría');
    }
}

