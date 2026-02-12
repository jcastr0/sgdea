<?php

namespace App\Console\Commands;

use App\Models\SetupCheckpoint;
use App\Services\SetupService;
use Illuminate\Console\Command;

class SetupAddStep extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'setup:add-step
        {--key= : Clave única del paso (ej: setup_step_ldap_configured)}
        {--name= : Nombre visible del paso (ej: Configurar LDAP)}
        {--phase= : Fase del setup (ej: FASE_2)}
        {--component= : Componente (ej: authentication)}
        {--order= : Orden del paso en la secuencia}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Agregar un nuevo paso al wizard de setup dinámicamente';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $key = $this->option('key') ?? $this->ask('Ingresa la clave única del paso');
        $name = $this->option('name') ?? $this->ask('Ingresa el nombre visible del paso');
        $phase = $this->option('phase') ?? $this->ask('Ingresa la fase (ej: FASE_2)');
        $component = $this->option('component') ?? $this->ask('Ingresa el componente');
        $order = $this->option('order') ?? $this->ask('Ingresa el orden del paso');

        // Validaciones
        if (!$key || !$name || !$phase || !$component || !$order) {
            $this->error('❌ Faltan datos requeridos');
            return 1;
        }

        if (!is_numeric($order)) {
            $this->error('❌ El orden debe ser un número');
            return 1;
        }

        $order = (int)$order;

        // Verificar si el paso ya existe
        if (SetupCheckpoint::where('step_key', $key)->exists()) {
            $this->error("❌ Ya existe un paso con la clave: {$key}");
            return 1;
        }

        // Usar SetupService para agregar el paso
        try {
            $setupService = new SetupService();
            $result = $setupService->addNewStep($key, $name, $phase, $component, $order);

            if ($result['success']) {
                $this->info('✅ Paso agregado exitosamente');
                $this->line('');
                $this->table(
                    ['Campo', 'Valor'],
                    [
                        ['Clave', $key],
                        ['Nombre', $name],
                        ['Fase', $phase],
                        ['Componente', $component],
                        ['Orden', $order],
                        ['Estado', 'pending'],
                    ]
                );
                return 0;
            } else {
                $this->error("❌ {$result['message']}");
                return 1;
            }
        } catch (\Exception $e) {
            $this->error("❌ Error: {$e->getMessage()}");
            return 1;
        }
    }
}

