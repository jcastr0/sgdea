<?php

namespace App\Http\Controllers;

use App\Models\Factura;
use App\Models\Tercero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    // Middleware se configura en las rutas en Laravel 12+

    /**
     * Mostrar dashboard principal
     */
    public function index()
    {
        $tenantId = session('tenant_id');
        $userId = auth()->user()->id;

        // Verificar si el usuario actual es admin del tenant
        $tenant = \App\Models\Tenant::find($tenantId);
        $isAdmin = $tenant && $tenant->superadmin_id === $userId;

        // Obtener usuarios pendientes (solo si es admin)
        $usuariosPendientes = 0;
        if ($isAdmin) {
            $usuariosPendientes = \App\Models\User::where('tenant_id', $tenantId)
                ->where('status', 'pending_approval')
                ->count();
        }

        return view('dashboard.index', [
            'tenantId' => $tenantId,
            'isAdmin' => $isAdmin,
            'usuariosPendientes' => $usuariosPendientes,
        ]);
    }

    /**
     * Obtener datos del dashboard (AJAX)
     */
    public function getData(Request $request)
    {
        $tenantId = session('tenant_id');

        $terceroId = $request->get('tercero_id');
        $fechaInicio = $request->get('fecha_inicio', Carbon::now()->startOfYear()->format('Y-m-d'));
        $fechaFin = $request->get('fecha_fin', Carbon::now()->format('Y-m-d'));

        // Validar que tercero pertenezca al tenant
        if ($terceroId) {
            $tercero = Tercero::where('id', $terceroId)
                ->where('tenant_id', $tenantId)
                ->first();

            if (!$tercero) {
                return response()->json(['error' => 'Acceso denegado'], 403);
            }
        }

        // Obtener base query
        $query = Factura::where('tenant_id', $tenantId)
            ->whereBetween('fecha_factura', [$fechaInicio, $fechaFin]);

        if ($terceroId) {
            $query->where('tercero_id', $terceroId);
        }

        // KPIs principales
        $kpis = $this->calcularKPIs($query, $tenantId, $terceroId, $fechaInicio, $fechaFin);

        // Gráficos
        $graficos = [
            'evolucion' => $this->obtenerEvolucionMensual($tenantId, $terceroId, $fechaInicio, $fechaFin),
            'top_terceros' => $this->obtenerTopTerceros($tenantId, $terceroId, $fechaInicio, $fechaFin),
            'distribucion_estados' => $this->obtenerDistribucionEstados($tenantId, $terceroId, $fechaInicio, $fechaFin),
            'facturas_mes' => $this->obtenerFacturasPorMes($tenantId, $terceroId, $fechaInicio, $fechaFin),
        ];

        // Tercero seleccionado
        $terceroSeleccionado = null;
        if ($terceroId) {
            $terceroSeleccionado = Tercero::find($terceroId);
        }

        return response()->json([
            'success' => true,
            'kpis' => $kpis,
            'graficos' => $graficos,
            'tercero' => $terceroSeleccionado,
            'periodo' => [
                'inicio' => $fechaInicio,
                'fin' => $fechaFin,
            ],
        ]);
    }

    /**
     * Buscar terceros (autocompletar)
     */
    public function buscarTerceros(Request $request)
    {
        $tenantId = session('tenant_id');
        $q = $request->get('q', '');

        $terceros = Tercero::where('tenant_id', $tenantId)
            ->where(function ($query) use ($q) {
                $query->where('nombre_razon_social', 'LIKE', "%{$q}%")
                    ->orWhere('nit', 'LIKE', "%{$q}%");
            })
            ->limit(10)
            ->get(['id', 'nombre_razon_social', 'nit']);

        return response()->json($terceros);
    }

    /**
     * Calcular KPIs principales
     */
    private function calcularKPIs($query, $tenantId, $terceroId, $fechaInicio, $fechaFin)
    {
        // Datos período actual
        $facturas = $query->get();

        $totalFacturas = $facturas->count();
        $totalIngresos = $facturas->sum('total_pagar');
        // Estados reales: aceptado, pendiente, rechazado
        $totalAceptadas = $facturas->where('estado', 'aceptado')->sum('total_pagar');
        $totalPendiente = $facturas->where('estado', 'pendiente')->sum('total_pagar');

        // Ventas netas (facturas aceptadas)
        $ventasNetas = $facturas->where('estado', 'aceptado')->sum('total_pagar');

        // Morosidad: % facturas vencidas sin pagar
        $ahora = Carbon::now();
        $facturasMorosas = $facturas->where('estado', 'pendiente')
            ->where('fecha_vencimiento', '<', $ahora)
            ->count();

        $tasaMorosidad = $totalFacturas > 0 ? round(($facturasMorosas / $totalFacturas) * 100, 2) : 0;

        // Comparativa con período anterior
        $diasRango = Carbon::parse($fechaInicio)->diffInDays(Carbon::parse($fechaFin));
        $fechaPreviaInicio = Carbon::parse($fechaInicio)->subDays($diasRango)->format('Y-m-d');
        $fechaPreviaFin = Carbon::parse($fechaInicio)->subDay()->format('Y-m-d');

        $queryPrevia = Factura::where('tenant_id', $tenantId)
            ->whereBetween('fecha_factura', [$fechaPreviaInicio, $fechaPreviaFin]);

        if ($terceroId) {
            $queryPrevia->where('tercero_id', $terceroId);
        }

        $ventasNetasPrevias = $queryPrevia->where('estado', 'aceptado')->sum('total_pagar');
        $cambioVentas = $ventasNetasPrevias > 0
            ? round((($ventasNetas - $ventasNetasPrevias) / $ventasNetasPrevias) * 100, 2)
            : 0;

        return [
            'ventas_netas' => [
                'valor' => $ventasNetas,
                'cambio' => $cambioVentas,
            ],
            'total_facturas' => $totalFacturas,
            'total_ingresos' => $totalIngresos,
            'total_pendiente' => $totalPendiente,
            'total_aceptadas' => $totalAceptadas,
            'tasa_morosidad' => $tasaMorosidad,
            'facturas_morosas' => $facturasMorosas,
        ];
    }

    /**
     * Obtener evolución de ventas por mes
     */
    private function obtenerEvolucionMensual($tenantId, $terceroId, $fechaInicio, $fechaFin)
    {
        $query = Factura::where('tenant_id', $tenantId)
            ->where('estado', 'aceptado')
            ->whereBetween('fecha_factura', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE_FORMAT(fecha_factura, "%Y-%m-01") as mes, SUM(total_pagar) as total')
            ->groupByRaw('DATE_FORMAT(fecha_factura, "%Y-%m-01")')
            ->orderBy('mes');

        if ($terceroId) {
            $query->where('tercero_id', $terceroId);
        }

        $datos = $query->get();

        return [
            'labels' => $datos->map(fn($d) => Carbon::parse($d->mes)->format('M Y'))->toArray(),
            'data' => $datos->map(fn($d) => round((float)$d->total, 2))->toArray(),
        ];
    }

    /**
     * Obtener top 5 terceros por ingresos
     */
    private function obtenerTopTerceros($tenantId, $terceroId, $fechaInicio, $fechaFin)
    {
        if ($terceroId) {
            // Si ya está filtrado por tercero, no mostrar top
            return [
                'labels' => [],
                'data' => [],
            ];
        }

        $datos = Factura::where('facturas.tenant_id', $tenantId)
            ->whereBetween('facturas.fecha_factura', [$fechaInicio, $fechaFin])
            ->join('terceros', 'facturas.tercero_id', '=', 'terceros.id')
            ->select('terceros.nombre_razon_social', 'terceros.id', DB::raw('SUM(facturas.total_pagar) as total'))
            ->groupBy('terceros.id', 'terceros.nombre_razon_social')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'labels' => $datos->map(fn($d) => substr($d->nombre_razon_social, 0, 15))->toArray(),
            'data' => $datos->map(fn($d) => round($d->total, 2))->toArray(),
            'ids' => $datos->map(fn($d) => $d->id)->toArray(),
        ];
    }

    /**
     * Obtener distribución de estados
     */
    private function obtenerDistribucionEstados($tenantId, $terceroId, $fechaInicio, $fechaFin)
    {
        $query = Factura::where('tenant_id', $tenantId)
            ->whereBetween('fecha_factura', [$fechaInicio, $fechaFin])
            ->select('estado', DB::raw('COUNT(*) as cantidad'))
            ->groupBy('estado');

        if ($terceroId) {
            $query->where('tercero_id', $terceroId);
        }

        $datos = $query->get();

        // Estados reales según importación Excel: aceptado, pendiente, rechazado
        $estados = [
            'aceptado' => 0,
            'pendiente' => 0,
            'rechazado' => 0,
        ];

        foreach ($datos as $d) {
            $estadoLower = strtolower($d->estado);
            if (isset($estados[$estadoLower])) {
                $estados[$estadoLower] = (int)$d->cantidad;
            }
        }

        return [
            'labels' => ['Aceptadas', 'Pendientes', 'Rechazadas'],
            'data' => [
                $estados['aceptado'],
                $estados['pendiente'],
                $estados['rechazado'],
            ],
        ];
    }

    /**
     * Obtener facturas por mes
     */
    private function obtenerFacturasPorMes($tenantId, $terceroId, $fechaInicio, $fechaFin)
    {
        $query = Factura::where('tenant_id', $tenantId)
            ->whereBetween('fecha_factura', [$fechaInicio, $fechaFin])
            ->selectRaw('DATE_FORMAT(fecha_factura, "%Y-%m-01") as mes, COUNT(*) as cantidad')
            ->groupByRaw('DATE_FORMAT(fecha_factura, "%Y-%m-01")')
            ->orderBy('mes');

        if ($terceroId) {
            $query->where('tercero_id', $terceroId);
        }

        $datos = $query->get();

        return [
            'labels' => $datos->map(fn($d) => Carbon::parse($d->mes)->format('M Y'))->toArray(),
            'data' => $datos->map(fn($d) => (int)$d->cantidad)->toArray(),
        ];
    }
}

