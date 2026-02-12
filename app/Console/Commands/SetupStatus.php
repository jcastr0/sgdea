<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SetupCheckpoint;
use App\Services\SetupService;

class SetupStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:status {--json : Mostrar salida en JSON}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Mostrar el estado actual del setup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $service = new SetupService();
        $progress = $service->getProgress();
        $steps = $service->getAllSteps();
        $completed = SetupCheckpoint::completed()->count();
        $total = SetupCheckpoint::count();

        if ($this->option('json')) {
            // Salida JSON
            $data = [
                'progress' => [
                    'percentage' => $progress->percentage ?? 0,
                    'current_step' => $progress->current_step ?? 1,
                    'total_steps' => $progress->total_steps ?? 6,
                    'completed' => $completed,
                ],
                'steps' => $steps->map(fn($s) => [
                    'order' => $s->step_order,
                    'name' => $s->step_name,
                    'key' => $s->step_key,
                    'status' => $s->status,
                    'component' => $s->component,
                    'phase' => $s->phase,
                ])->toArray(),
                'setup_completed' => $service->isSetupComplete(),
            ];
            $this->line(json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
            return Command::SUCCESS;
        }

        // Salida formateada
        $this->info('📊 Estado del Setup SGDEA');
        $this->newLine();

        // Barra de progreso
        $percentage = $progress->percentage ?? 0;
        $currentStep = $progress->current_step ?? 1;
        $totalSteps = $progress->total_steps ?? 6;
        $barLength = 30;
        $filled = (int) ($percentage / 100 * $barLength);
        $bar = str_repeat('█', $filled) . str_repeat('░', $barLength - $filled);
        $this->line("Progreso: [$bar] $percentage%");
        $this->line("Paso $currentStep/$totalSteps");

        $this->newLine();
        $this->info('Pasos:');

        // Tabla de pasos
        $tableData = $steps->map(fn($s) => [
            $s->step_order,
            $s->step_name,
            match($s->status) {
                'completed' => '✓ Completado',
                'failed' => '✗ Fallido',
                'pending' => '○ Pendiente',
                default => $s->status
            },
            $s->component,
            $s->completion_date ? $s->completion_date->format('Y-m-d H:i') : '-',
        ])->toArray();

        $this->table(
            ['#', 'Paso', 'Estado', 'Componente', 'Fecha'],
            $tableData
        );

        $this->newLine();

        if ($service->isSetupComplete()) {
            $this->line('<info>✓ Setup completado</info>');
            if (file_exists(storage_path('.setup_completed'))) {
                $info = json_decode(file_get_contents(storage_path('.setup_completed')), true);
                $this->line('Completado en: ' . $info['completed_at']);
            }
        } else {
            $nextStep = $service->getNextStep();
            if ($nextStep) {
                $this->newLine();
                $this->warn("⏭️  Siguiente paso: {$nextStep->step_name}");
                $this->line("Accede a: http://localhost:8080/setup");
            }
        }

        return Command::SUCCESS;
    }
}

