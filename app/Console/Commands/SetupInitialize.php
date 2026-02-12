<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SetupCheckpoint;
use App\Models\SetupProgress;
use App\Services\SetupService;

class SetupInitialize extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:initialize {--force : Reinicializar si ya existe}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Inicializar los pasos de setup del sistema SGDEA (FASE 1)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🚀 Inicializando Sistema de Setup SGDEA - FASE 1');
        $this->newLine();

        // Verificar si ya existen checkpoints
        $existingCount = SetupCheckpoint::count();
        if ($existingCount > 0 && !$this->option('force')) {
            $this->warn("⚠️  Ya existen $existingCount checkpoints en la BD.");
            if (!$this->confirm('¿Deseas continuar y crear duplicados?')) {
                $this->info('❌ Operación cancelada.');
                return Command::FAILURE;
            }
        }

        if ($existingCount > 0 && $this->option('force')) {
            $this->line('Eliminando checkpoints existentes...');
            SetupCheckpoint::truncate();
            SetupProgress::truncate();
            $this->info('✓ Datos anteriores eliminados');
        }

        // Inicializar checkpoints
        try {
            $this->line('Creando 6 pasos de FASE 1...');
            $service = new SetupService();
            $service->initializeCheckpoints();

            $this->info('✓ Checkpoints creados exitosamente');
            $this->newLine();

            // Mostrar tabla de pasos
            $steps = SetupCheckpoint::orderBy('step_order')->get();
            $this->table(
                ['Orden', 'Paso', 'Clave', 'Estado', 'Componente'],
                $steps->map(fn($s) => [
                    $s->step_order,
                    $s->step_name,
                    $s->step_key,
                    $s->status,
                    $s->component,
                ])->toArray()
            );

            $this->newLine();
            $this->info('✓ Sistema inicializado correctamente');
            $this->line('Accede a: http://localhost:8080/setup para comenzar');

            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error durante la inicialización: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

