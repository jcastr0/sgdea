<?php

namespace App\Services\Admin;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Factura;
use App\Models\AuditLog;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

/**
 * ============================================================================
 * SERVICE: SystemStatsService
 * ============================================================================
 *
 * Servicio para obtener estadísticas globales del sistema.
 * Usado principalmente por el Dashboard del Superadmin Global.
 *
 * @author SGDEA Team
 * ============================================================================
 */
class SystemStatsService
{
    /**
     * Obtener KPIs globales del sistema
     *
     * @return array
     */
    public function getGlobalKPIs(): array
    {
        // Conteos básicos
        $totalTenants = Tenant::count();
        $totalUsers = User::count();
        $totalFacturas = Factura::count();

        // Calcular storage usado (aproximado basado en PDFs)
        $storageUsed = $this->calculateStorageUsed();

        // Variaciones del último mes
        $lastMonth = Carbon::now()->subMonth();
        $tenantsLastMonth = Tenant::where('created_at', '>=', $lastMonth)->count();
        $usersLastMonth = User::where('created_at', '>=', $lastMonth)->count();
        $facturasLastMonth = Factura::where('created_at', '>=', $lastMonth)->count();

        return [
            'total_tenants' => $totalTenants,
            'total_users' => $totalUsers,
            'total_facturas' => $totalFacturas,
            'storage_used' => $storageUsed,
            'storage_used_formatted' => $this->formatBytes($storageUsed),
            'tenants_new' => $tenantsLastMonth,
            'users_new' => $usersLastMonth,
            'facturas_new' => $facturasLastMonth,
        ];
    }

    /**
     * Obtener distribución de tenants por estado
     *
     * @return array
     */
    public function getTenantsByStatus(): array
    {
        $statuses = Tenant::select('status', DB::raw('count(*) as total'))
            ->groupBy('status')
            ->pluck('total', 'status')
            ->toArray();

        return [
            'active' => $statuses['active'] ?? 0,
            'suspended' => $statuses['suspended'] ?? 0,
            'trial' => $statuses['trial'] ?? 0,
            'inactive' => $statuses['inactive'] ?? 0,
        ];
    }

    /**
     * Obtener tendencia de crecimiento (nuevos tenants y usuarios por mes)
     *
     * @param int $months Número de meses hacia atrás
     * @return array
     */
    public function getGrowthTrend(int $months = 6): array
    {
        $labels = [];
        $tenantsData = [];
        $usersData = [];
        $facturasData = [];

        for ($i = $months - 1; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $startOfMonth = $date->copy()->startOfMonth();
            $endOfMonth = $date->copy()->endOfMonth();

            $labels[] = $date->translatedFormat('M Y');

            $tenantsData[] = Tenant::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $usersData[] = User::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
            $facturasData[] = Factura::whereBetween('created_at', [$startOfMonth, $endOfMonth])->count();
        }

        return [
            'labels' => $labels,
            'tenants' => $tenantsData,
            'users' => $usersData,
            'facturas' => $facturasData,
        ];
    }

    /**
     * Obtener top tenants por facturas
     *
     * @param int $limit
     * @return Collection
     */
    public function getTopTenants(int $limit = 5): Collection
    {
        return Tenant::select('tenants.*')
            ->selectRaw('(SELECT COUNT(*) FROM facturas WHERE facturas.tenant_id = tenants.id) as facturas_count')
            ->selectRaw('(SELECT COUNT(*) FROM users WHERE users.tenant_id = tenants.id) as users_count')
            ->selectRaw('(SELECT COALESCE(SUM(total_pagar), 0) FROM facturas WHERE facturas.tenant_id = tenants.id) as total_facturado')
            ->orderByDesc('facturas_count')
            ->limit($limit)
            ->get();
    }

    /**
     * Obtener alertas del sistema
     *
     * @return array
     */
    public function getSystemAlerts(): array
    {
        $alerts = [];

        // Tenants sin actividad en los últimos 30 días
        $inactiveTenants = Tenant::where('status', 'active')
            ->whereDoesntHave('facturas', function ($query) {
                $query->where('created_at', '>=', Carbon::now()->subDays(30));
            })
            ->count();

        if ($inactiveTenants > 0) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'clock',
                'message' => "{$inactiveTenants} tenant(s) sin actividad en 30 días",
                'action' => route('admin.tenants.index', ['inactive' => true]),
            ];
        }

        // Usuarios pendientes de aprobación
        $pendingUsers = User::where('status', 'pending')->count();
        if ($pendingUsers > 0) {
            $alerts[] = [
                'type' => 'info',
                'icon' => 'user-check',
                'message' => "{$pendingUsers} usuario(s) pendientes de aprobación",
                'action' => route('admin.usuarios.pendientes'),
            ];
        }

        // Verificar storage
        $storageUsed = $this->calculateStorageUsed();
        $storageLimit = 50 * 1024 * 1024 * 1024; // 50GB límite ejemplo
        $storagePercent = ($storageUsed / $storageLimit) * 100;

        if ($storagePercent > 80) {
            $alerts[] = [
                'type' => $storagePercent > 90 ? 'danger' : 'warning',
                'icon' => 'database',
                'message' => "Storage al " . round($storagePercent) . "% de capacidad",
                'action' => null,
            ];
        }

        // Si no hay alertas, mostrar mensaje positivo
        if (empty($alerts)) {
            $alerts[] = [
                'type' => 'success',
                'icon' => 'check-circle',
                'message' => "Todo funcionando correctamente",
                'action' => null,
            ];
        }

        return $alerts;
    }

    /**
     * Obtener actividad reciente global
     *
     * @param int $limit
     * @return Collection
     */
    public function getRecentActivity(int $limit = 10): Collection
    {
        return AuditLog::with(['user', 'user.tenant'])
            ->whereIn('action', ['login', 'create', 'update', 'delete', 'import'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get()
            ->map(function ($log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'action_label' => $this->getActionLabel($log->action),
                    'action_color' => $this->getActionColor($log->action),
                    'user_name' => $log->user?->name ?? 'Sistema',
                    'tenant_name' => $log->user?->tenant?->name ?? 'Global',
                    'model' => $log->model_type ? class_basename($log->model_type) : null,
                    'model_id' => $log->model_id,
                    'ip_address' => $log->ip_address,
                    'created_at' => $log->created_at,
                    'time_ago' => $log->created_at->diffForHumans(),
                ];
            });
    }

    /**
     * Calcular storage usado
     *
     * @return int Bytes usados
     */
    protected function calculateStorageUsed(): int
    {
        $totalBytes = 0;

        // Contar archivos en storage/app/private/facturas
        $path = storage_path('app/private/facturas');
        if (is_dir($path)) {
            $totalBytes += $this->getDirectorySize($path);
        }

        // Contar archivos en storage/app/public
        $path = storage_path('app/public');
        if (is_dir($path)) {
            $totalBytes += $this->getDirectorySize($path);
        }

        return $totalBytes;
    }

    /**
     * Obtener tamaño de un directorio
     *
     * @param string $path
     * @return int
     */
    protected function getDirectorySize(string $path): int
    {
        $size = 0;

        foreach (new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($path)) as $file) {
            if ($file->isFile()) {
                $size += $file->getSize();
            }
        }

        return $size;
    }

    /**
     * Formatear bytes a unidades legibles
     *
     * @param int $bytes
     * @return string
     */
    protected function formatBytes(int $bytes): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Obtener etiqueta de acción para auditoría
     *
     * @param string $action
     * @return string
     */
    protected function getActionLabel(string $action): string
    {
        return match ($action) {
            'login' => 'Inicio de sesión',
            'logout' => 'Cierre de sesión',
            'create' => 'Creación',
            'update' => 'Actualización',
            'delete' => 'Eliminación',
            'import' => 'Importación',
            default => ucfirst($action),
        };
    }

    /**
     * Obtener color de acción para auditoría
     *
     * @param string $action
     * @return string
     */
    protected function getActionColor(string $action): string
    {
        return match ($action) {
            'login' => 'emerald',
            'logout' => 'gray',
            'create' => 'green',
            'update' => 'blue',
            'delete' => 'red',
            'import' => 'indigo',
            default => 'gray',
        };
    }
}

