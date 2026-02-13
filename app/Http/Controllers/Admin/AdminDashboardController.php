<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Admin\SystemStatsService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * ============================================================================
 * CONTROLLER: AdminDashboardController
 * ============================================================================
 *
 * Controlador del Dashboard para Superadmin Global.
 * Proporciona vista y endpoints API para estadísticas del sistema.
 *
 * @author SGDEA Team
 * ============================================================================
 */
class AdminDashboardController extends Controller
{
    protected SystemStatsService $statsService;

    public function __construct(SystemStatsService $statsService)
    {
        $this->statsService = $statsService;
    }

    /**
     * Dashboard principal del Admin Global
     */
    public function index()
    {
        // El middleware IsSuperadminGlobal ya verificó la autenticación
        $adminGlobal = auth()->user();

        // Obtener estadísticas
        $kpis = $this->statsService->getGlobalKPIs();
        $tenantsByStatus = $this->statsService->getTenantsByStatus();
        $growthTrend = $this->statsService->getGrowthTrend(6);
        $topTenants = $this->statsService->getTopTenants(5);
        $alerts = $this->statsService->getSystemAlerts();
        $recentActivity = $this->statsService->getRecentActivity(10);

        return view('admin.dashboard', [
            'adminGlobal' => $adminGlobal,
            'kpis' => $kpis,
            'tenantsByStatus' => $tenantsByStatus,
            'growthTrend' => $growthTrend,
            'topTenants' => $topTenants,
            'alerts' => $alerts,
            'recentActivity' => $recentActivity,
        ]);
    }

    /**
     * API: Obtener KPIs en tiempo real
     */
    public function getStats(): JsonResponse
    {
        return response()->json([
            'kpis' => $this->statsService->getGlobalKPIs(),
            'tenants_by_status' => $this->statsService->getTenantsByStatus(),
        ]);
    }

    /**
     * API: Obtener alertas activas
     */
    public function getAlerts(): JsonResponse
    {
        return response()->json([
            'alerts' => $this->statsService->getSystemAlerts(),
        ]);
    }

    /**
     * API: Obtener actividad reciente
     */
    public function getActivity(): JsonResponse
    {
        return response()->json([
            'activity' => $this->statsService->getRecentActivity(15),
        ]);
    }

    /**
     * API: Obtener tendencia de crecimiento
     */
    public function getGrowthTrend(Request $request): JsonResponse
    {
        $months = $request->input('months', 6);

        return response()->json([
            'trend' => $this->statsService->getGrowthTrend($months),
        ]);
    }
}

