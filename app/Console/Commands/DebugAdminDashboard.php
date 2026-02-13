<?php

namespace App\Console\Commands;

use App\Services\Admin\SystemStatsService;
use Illuminate\Console\Command;

class DebugAdminDashboard extends Command
{
    protected $signature = 'debug:admin-dashboard {--test=all : Test a ejecutar (all, kpis, tenants, growth, alerts, activity)}';
    protected $description = 'Debug del dashboard de superadmin';

    public function handle()
    {
        $test = $this->option('test');
        $service = app(SystemStatsService::class);

        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('🔧 DEBUG ADMIN DASHBOARD');
        $this->info('═══════════════════════════════════════════════════════════');

        if ($test === 'all' || $test === 'kpis') {
            $this->newLine();
            $this->info('📊 KPIs GLOBALES');
            $this->line('───────────────────────────────────────────────────────────');

            $kpis = $service->getGlobalKPIs();
            $this->table(
                ['Métrica', 'Valor'],
                [
                    ['Total Tenants', $kpis['total_tenants']],
                    ['Total Usuarios', $kpis['total_users']],
                    ['Total Facturas', $kpis['total_facturas']],
                    ['Storage Usado', $kpis['storage_used_formatted']],
                    ['Tenants Nuevos (mes)', $kpis['tenants_new']],
                    ['Usuarios Nuevos (mes)', $kpis['users_new']],
                    ['Facturas Nuevas (mes)', $kpis['facturas_new']],
                ]
            );
        }

        if ($test === 'all' || $test === 'tenants') {
            $this->newLine();
            $this->info('🏢 TENANTS POR ESTADO');
            $this->line('───────────────────────────────────────────────────────────');

            $tenantsByStatus = $service->getTenantsByStatus();
            $this->table(
                ['Estado', 'Cantidad'],
                [
                    ['Activos', $tenantsByStatus['active']],
                    ['Suspendidos', $tenantsByStatus['suspended']],
                    ['En Prueba', $tenantsByStatus['trial']],
                    ['Inactivos', $tenantsByStatus['inactive']],
                ]
            );
        }

        if ($test === 'all' || $test === 'growth') {
            $this->newLine();
            $this->info('📈 TENDENCIA DE CRECIMIENTO');
            $this->line('───────────────────────────────────────────────────────────');

            $growth = $service->getGrowthTrend(6);
            $rows = [];
            for ($i = 0; $i < count($growth['labels']); $i++) {
                $rows[] = [
                    $growth['labels'][$i],
                    $growth['tenants'][$i],
                    $growth['users'][$i],
                    $growth['facturas'][$i],
                ];
            }
            $this->table(['Mes', 'Tenants', 'Usuarios', 'Facturas'], $rows);
        }

        if ($test === 'all' || $test === 'alerts') {
            $this->newLine();
            $this->info('⚠️  ALERTAS DEL SISTEMA');
            $this->line('───────────────────────────────────────────────────────────');

            $alerts = $service->getSystemAlerts();
            foreach ($alerts as $alert) {
                $icon = match($alert['type']) {
                    'success' => '✅',
                    'warning' => '⚠️',
                    'danger' => '🔴',
                    'info' => 'ℹ️',
                    default => '•',
                };
                $this->line("  {$icon} {$alert['message']}");
            }
        }

        if ($test === 'all' || $test === 'activity') {
            $this->newLine();
            $this->info('📋 ACTIVIDAD RECIENTE');
            $this->line('───────────────────────────────────────────────────────────');

            $activity = $service->getRecentActivity(5);
            $rows = [];
            foreach ($activity as $act) {
                $rows[] = [
                    $act['action_label'],
                    $act['user_name'],
                    $act['tenant_name'],
                    $act['model'] ?? '-',
                    $act['time_ago'],
                ];
            }
            $this->table(['Acción', 'Usuario', 'Tenant', 'Modelo', 'Tiempo'], $rows);
        }

        $this->newLine();
        $this->info('═══════════════════════════════════════════════════════════');
        $this->info('✅ Debug completado');
        $this->info('═══════════════════════════════════════════════════════════');

        return 0;
    }
}
