<?php

namespace Database\Seeders;

use App\Models\SetupCheckpoint;
use Illuminate\Database\Seeder;

class SetupCheckpointSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $checkpoints = [
            [
                'step_key' => 'setup_step_superadmin_created',
                'step_name' => 'Crear Superadmin Global',
                'step_order' => 1,
                'phase' => 'FASE_1',
                'component' => 'superadmin',
                'status' => 'pending',
            ],
            [
                'step_key' => 'setup_step_mysql_connected',
                'step_name' => 'Conectar MySQL',
                'step_order' => 2,
                'phase' => 'FASE_1',
                'component' => 'database',
                'status' => 'pending',
            ],
            [
                'step_key' => 'setup_step_first_tenant_and_theme',
                'step_name' => 'Crear Primer Tenant y Tema',
                'step_order' => 3,
                'phase' => 'FASE_1',
                'component' => 'tenant_theme',
                'status' => 'pending',
            ],
            [
                'step_key' => 'setup_step_email_configured',
                'step_name' => 'Configurar Email (Opcional)',
                'step_order' => 4,
                'phase' => 'FASE_1',
                'component' => 'email',
                'status' => 'pending',
            ],
            [
                'step_key' => 'setup_step_ldap_configured',
                'step_name' => 'Configurar LDAP (Opcional)',
                'step_order' => 5,
                'phase' => 'FASE_1',
                'component' => 'ldap',
                'status' => 'pending',
            ],
            [
                'step_key' => 'setup_step_verification_passed',
                'step_name' => 'Verificación Final',
                'step_order' => 6,
                'phase' => 'FASE_1',
                'component' => 'verification',
                'status' => 'pending',
            ],
        ];

        foreach ($checkpoints as $checkpoint) {
            SetupCheckpoint::updateOrCreate(
                ['step_key' => $checkpoint['step_key']],
                $checkpoint
            );
        }
    }
}
