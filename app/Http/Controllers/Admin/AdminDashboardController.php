<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Tenant;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    /**
     * Dashboard del Admin Global
     */
    public function index()
    {
        // Verificar que está autenticado como admin global
        if (!auth('system')->check()) {
            abort(401, 'No autorizado');
        }

        $adminGlobal = auth('system')->user();

        // Obtener estadísticas
        $totalTenants = Tenant::count();
        $activeTenants = Tenant::where('status', 'active')->count();
        $totalUsers = \DB::table('users')->count();
        $recentTenants = Tenant::with('systemUser')
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('admin.dashboard', [
            'adminGlobal' => $adminGlobal,
            'totalTenants' => $totalTenants,
            'activeTenants' => $activeTenants,
            'totalUsers' => $totalUsers,
            'recentTenants' => $recentTenants,
        ]);
    }
}

