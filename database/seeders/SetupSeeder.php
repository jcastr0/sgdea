<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * SetupSeeder
 *
 * Crea los checkpoints del asistente de configuración inicial.
 * Estos checkpoints rastrean el progreso del setup del sistema.
 */
class SetupSeeder extends Seeder
{
    /**
     * Definición de checkpoints del setup
     */
    private array $checkpoints = [
        [
            'key' => 'database_configured',
            'name' => 'Base de datos configurada',
            'description' => 'La conexión a la base de datos está funcionando correctamente',
            'order' => 1,
            'is_required' => true,
        ],
        [
            'key' => 'admin_created',
            'name' => 'Administrador creado',
            'description' => 'Se ha creado al menos un usuario administrador',
            'order' => 2,
            'is_required' => true,
        ],
        [
            'key' => 'tenant_configured',
            'name' => 'Empresa configurada',
            'description' => 'La información de la empresa ha sido configurada',
            'order' => 3,
            'is_required' => true,
        ],
        [
            'key' => 'theme_configured',
            'name' => 'Tema configurado',
            'description' => 'Se han configurado los colores y estilos del sistema',
            'order' => 4,
            'is_required' => false,
        ],
        [
            'key' => 'setup_completed',
            'name' => 'Setup completado',
            'description' => 'El asistente de configuración ha finalizado',
            'order' => 5,
            'is_required' => true,
        ],
    ];

    public function run(): void
    {
        $this->command->info('⚙️  Creando checkpoints del setup...');

        foreach ($this->checkpoints as $checkpoint) {
            DB::table('setup_checkpoints')->updateOrInsert(
                ['key' => $checkpoint['key']],
                [
                    'name' => $checkpoint['name'],
                    'description' => $checkpoint['description'],
                    'order' => $checkpoint['order'],
                    'is_required' => $checkpoint['is_required'],
                    'is_completed' => true, // Marcamos como completados ya que el seeder configura todo
                    'completed_at' => now(),
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }

        // Crear registro de progreso
        DB::table('setup_progress')->updateOrInsert(
            ['id' => 1],
            [
                'current_step' => 'completed',
                'step_number' => 5,
                'total_steps' => 5,
                'is_completed' => true,
                'started_at' => now(),
                'completed_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]
        );

        $this->command->info('   ✅ ' . count($this->checkpoints) . ' checkpoints creados');
        $this->command->info('   ✅ Setup marcado como completado');
    }
}

