<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Console\Command;

class FixRolePermissions extends Command
{
    protected $signature = 'fix:role-permissions
                            {--tenant= : ID del tenant específico (opcional)}
                            {--dry-run : Solo mostrar qué haría}';

    protected $description = 'Asignar permisos faltantes a roles administrador';

    public function handle(): int
    {
        $this->info('🔧 Corrigiendo permisos de roles administrador...');
        $this->newLine();

        // Obtener permisos (excluir admin global)
        $permissions = Permission::whereNotIn('resource', ['admin'])->pluck('id');
        $this->line("📋 Total de permisos a asignar: {$permissions->count()}");
        $this->newLine();

        // Obtener roles administrador
        $query = Role::where('slug', 'administrador');

        if ($this->option('tenant')) {
            $query->where('tenant_id', $this->option('tenant'));
        }

        $roles = $query->get();

        if ($roles->isEmpty()) {
            $this->warn('No se encontraron roles administrador.');
            return Command::SUCCESS;
        }

        foreach ($roles as $role) {
            $currentCount = $role->permissions()->count();
            $this->line("Rol ID {$role->id} (Tenant {$role->tenant_id}): {$currentCount} permisos actuales");

            if ($this->option('dry-run')) {
                $this->line("   → [DRY-RUN] Se asignarían {$permissions->count()} permisos");
            } else {
                $role->permissions()->syncWithoutDetaching($permissions);
                $newCount = $role->permissions()->count();
                $this->info("   ✅ Actualizado a {$newCount} permisos");
            }
        }

        $this->newLine();
        $this->info('✅ Proceso completado');

        return Command::SUCCESS;
    }
}

