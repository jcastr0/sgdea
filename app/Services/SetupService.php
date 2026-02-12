<?php

namespace App\Services;

use App\Models\SetupCheckpoint;
use App\Models\SetupProgress;
use Illuminate\Support\Facades\DB;

class SetupService
{
    /**
     * Obtener siguiente paso pendiente
     */
    public function getNextStep()
    {
        return SetupCheckpoint::pending()->first();
    }

    /**
     * Obtener todos los pasos ordenados
     */
    public function getAllSteps()
    {
        return SetupCheckpoint::orderBy('step_order')->get();
    }

    /**
     * Marcar paso como completado
     */
    public function completeStep($stepKey)
    {
        try {
            $checkpoint = SetupCheckpoint::where('step_key', $stepKey)->first();

            if (!$checkpoint) {
                return [
                    'success' => false,
                    'message' => 'Checkpoint no encontrado',
                ];
            }

            $checkpoint->update([
                'status' => 'completed',
                'completion_date' => now(),
                'error_message' => null,
            ]);

            $this->updateProgress();

            return [
                'success' => true,
                'message' => 'Paso completado exitosamente',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al completar paso: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Marcar paso como fallido
     */
    public function failStep($stepKey, $error = null)
    {
        try {
            $checkpoint = SetupCheckpoint::where('step_key', $stepKey)->first();

            if (!$checkpoint) {
                return [
                    'success' => false,
                    'message' => 'Checkpoint no encontrado',
                ];
            }

            $checkpoint->update([
                'status' => 'failed',
                'error_message' => $error ?? 'Error desconocido',
            ]);

            $this->updateProgress();

            return [
                'success' => true,
                'message' => 'Paso marcado como fallido',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al marcar paso como fallido: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Actualizar progreso general
     */
    public function updateProgress()
    {
        $total = SetupCheckpoint::countTotal();
        $completed = SetupCheckpoint::countCompleted();
        $percentage = $total > 0 ? (int) (($completed / $total) * 100) : 0;

        $progress = SetupProgress::first();

        if ($progress) {
            $progress->update([
                'total_steps' => $total,
                'current_step' => $completed + 1,
                'percentage' => $percentage,
            ]);
        } else {
            SetupProgress::create([
                'current_step' => $completed + 1,
                'total_steps' => $total,
                'percentage' => $percentage,
            ]);
        }
    }

    /**
     * Agregar nuevo paso (para futuras fases)
     */
    public function addNewStep($stepKey, $stepName, $phase, $component, $order)
    {
        try {
            SetupCheckpoint::create([
                'step_key' => $stepKey,
                'step_name' => $stepName,
                'step_order' => $order,
                'phase' => $phase,
                'component' => $component,
                'status' => 'pending',
            ]);

            $this->updateProgress();

            return [
                'success' => true,
                'message' => 'Nuevo paso agregado',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al agregar paso: ' . $e->getMessage(),
            ];
        }
    }

    /**
     * Verificar si setup está completo
     */
    public function isSetupComplete()
    {
        return SetupCheckpoint::pending()->count() === 0;
    }

    /**
     * Inicializar checkpoints de FASE 1
     */
    public function initializeCheckpoints()
    {
        $checkpoints = [
            [
                'step_key' => 'setup_step_superadmin_created',
                'step_name' => 'Administrador Global',
                'step_order' => 1,
                'phase' => 'FASE_1',
                'component' => 'Superadmin del Sistema',
                'optional' => false,
            ],
            [
                'step_key' => 'setup_step_mysql_connected',
                'step_name' => 'Base de Datos',
                'step_order' => 2,
                'phase' => 'FASE_1',
                'component' => 'Configurar conexión MySQL',
                'optional' => false,
            ],
            [
                'step_key' => 'setup_step_theme_applied',
                'step_name' => 'Tema y Logo',
                'step_order' => 3,
                'phase' => 'FASE_1',
                'component' => 'Configurar tema visual',
                'optional' => false,
            ],
            [
                'step_key' => 'setup_step_first_tenant_created',
                'step_name' => 'Primera Empresa',
                'step_order' => 4,
                'phase' => 'FASE_1',
                'component' => 'Crear primera empresa y superadmin',
                'optional' => false,
            ],
            [
                'step_key' => 'setup_step_email_configured',
                'step_name' => 'Configuración Email',
                'step_order' => 5,
                'phase' => 'FASE_1',
                'component' => 'Configurar notificaciones por email',
                'optional' => true,
            ],
            [
                'step_key' => 'setup_step_ldap_configured',
                'step_name' => 'Configuración LDAP',
                'step_order' => 6,
                'phase' => 'FASE_1',
                'component' => 'Configurar autenticación LDAP',
                'optional' => true,
            ],
            [
                'step_key' => 'setup_step_verification_passed',
                'step_name' => 'Verificación Final',
                'step_order' => 7,
                'phase' => 'FASE_1',
                'component' => 'Verificación y finalización',
                'optional' => false,
            ],
        ];

        foreach ($checkpoints as $checkpoint) {
            SetupCheckpoint::firstOrCreate(
                ['step_key' => $checkpoint['step_key']],
                array_merge($checkpoint, ['status' => 'pending'])
            );
        }

        $this->updateProgress();
    }

    /**
     * Obtener progreso actual
     */
    public function getProgress()
    {
        return SetupProgress::first() ?? SetupProgress::create([
            'current_step' => 1,
            'total_steps' => SetupCheckpoint::countTotal(),
            'percentage' => 0,
        ]);
    }

    /**
     * Obtener pasos por fase
     */
    public function getStepsByPhase($phase)
    {
        return SetupCheckpoint::byPhase($phase)->get();
    }

    /**
     * Obtener el paso actual (máximo permitido)
     * El usuario puede acceder hasta el paso completado + 1
     */
    public function getCurrentAllowedStep()
    {
        // Obtener el último paso completado
        $lastCompleted = SetupCheckpoint::where('status', 'completed')
            ->orderBy('step_order', 'desc')
            ->first();

        if (!$lastCompleted) {
            // Si no hay ninguno completado, puede acceder al paso 1
            return 1;
        }

        // El paso permitido es el siguiente al último completado
        // o el mismo si es el último disponible
        $nextOrder = $lastCompleted->step_order + 1;
        $maxOrder = SetupCheckpoint::max('step_order');

        return min($nextOrder, $maxOrder);
    }

    /**
     * Validar que el usuario pueda acceder a un paso específico
     * Solo puede acceder al paso actual o anteriores (si están completados)
     */
    public function canAccessStep($stepOrder)
    {
        $currentAllowed = $this->getCurrentAllowedStep();

        // Puede acceder si es igual o menor al permitido
        return $stepOrder <= $currentAllowed;
    }

    /**
     * Obtener el paso anterior permitido (para el botón "Volver atrás")
     */
    public function getPreviousStep($currentStepOrder)
    {
        if ($currentStepOrder <= 1) {
            return null; // No hay paso anterior
        }

        return SetupCheckpoint::where('step_order', $currentStepOrder - 1)->first();
    }

    /**
     * Marcar un paso como "no completado" (para volver atrás)
     * Cuando vuelves atrás, el paso actual vuelve a "pending"
     */
    public function revertStep($stepKey)
    {
        try {
            $checkpoint = SetupCheckpoint::where('step_key', $stepKey)->first();

            if (!$checkpoint) {
                return [
                    'success' => false,
                    'message' => 'Checkpoint no encontrado',
                ];
            }

            $checkpoint->update([
                'status' => 'pending',
                'completion_date' => null,
                'error_message' => null,
            ]);

            $this->updateProgress();

            return [
                'success' => true,
                'message' => 'Paso revertido correctamente',
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Error al revertir paso: ' . $e->getMessage(),
            ];
        }
    }
}
