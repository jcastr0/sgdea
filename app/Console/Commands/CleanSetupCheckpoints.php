<?php

namespace App\Console\Commands;

use App\Models\SetupCheckpoint;
use Illuminate\Console\Command;

class CleanSetupCheckpoints extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:clean {--reset : Borrar todos y recrear}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Limpiar y verificar checkpoints del setup';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('🔍 Verificando Setup Checkpoints...');

        $total = SetupCheckpoint::count();
        $this->info("Total de checkpoints en BD: {$total}");

        if ($total === 0) {
            $this->info('✓ Sin datos de setup previos.');
            return;
        }

        // Contar por step_key (detectar duplicados)
        $groupedByKey = SetupCheckpoint::selectRaw('step_key, COUNT(*) as count')
            ->groupBy('step_key')
            ->having('count', '>', 1)
            ->get();

        if ($groupedByKey->isNotEmpty()) {
            $this->warn('⚠️  DETECTADOS DUPLICADOS:');
            foreach ($groupedByKey as $item) {
                $this->warn("   - {$item->step_key}: {$item->count} registros");
            }
        } else {
            $this->info('✓ Sin duplicados detectados.');
        }

        // Mostrar checkpoints actuales
        $this->info("\nCheckpoints actuales:");
        SetupCheckpoint::orderBy('step_order')->get()->each(function ($checkpoint) {
            $status = match ($checkpoint->status) {
                'pending' => '⏳',
                'completed' => '✓',
                'failed' => '✗',
                default => '?',
            };
            $this->line("   {$status} {$checkpoint->step_order}. {$checkpoint->step_key} ({$checkpoint->status})");
        });

        if ($this->option('reset')) {
            if ($this->confirm('🚨 ¿Borrar TODOS los checkpoints y recrear?')) {
                SetupCheckpoint::truncate();
                $this->info('✓ Checkpoints borrados');

                // Recrear con SetupService
                app('App\Services\SetupService')->initializeCheckpoints();
                $this->info('✓ Checkpoints recreados');
                $this->info('✅ Setup limpio y listo');
            }
        } else {
            if ($total > 6) {
                $this->warn("\n⚠️  Tienes {$total} checkpoints pero deberías tener solo 6");
                $this->info('Ejecuta: php artisan setup:clean --reset');
            }
        }
    }
}

