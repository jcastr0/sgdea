<?php

namespace App\Services;

use App\Models\Factura;
use App\Models\Tercero;
use Illuminate\Support\Facades\Cache;

class DashboardService
{
    /**
     * Caché de 5 minutos para KPIs (para optimizar rendimiento)
     */
    const CACHE_TTL = 300;

    /**
     * Obtiene todos los KPIs consolidados o filtrados
     */
    public function getKPIs(?int $terceroId = null): array
    {
        $cacheKey = $this->getCacheKey('kpis', $terceroId);

        return Cache::remember($cacheKey, self::CACHE_TTL, function () use ($terceroId) {
            return [
                'total_facturado_neto' => $this->getTotalFacturadoNeto($terceroId),
                'total_facturas_emitidas' => $this->getTotalFacturasEmitidas($terceroId),
                'total_clientes' => $this->getTotalClientes($terceroId),
                'promedio_facturado' => $this->getPromedioFacturado($terceroId),
                'total_recaudado' => $this->getTotalRecaudado($terceroId),
                'total_pendiente' => $this->getTotalPendiente($terceroId),
            ];
        });
    }

    /**
     * Calcula el Total Facturado Neto
     * Ventas Aceptadas - Notas de Crédito Aceptadas
     */
    public function getTotalFacturadoNeto(?int $terceroId = null): float
    {
        $query = Factura::aceptadas();

        if ($terceroId) {
            $query->where('tercero_id', $terceroId);
        }

        $ventasAceptadas = (clone $query)
            ->deVenta()
            ->sum('total_pagar');

        $notasCreditoAceptadas = (clone $query)
            ->notasCredito()
            ->sum('total_pagar');

        $neto = $ventasAceptadas - $notasCreditoAceptadas;

        return max(0, $neto); // Asegurar que nunca sea negativo
    }

    /**
     * Obtiene el Total de Facturas Emitidas (solo Facturas de Venta)
     */
    public function getTotalFacturasEmitidas(?int $terceroId = null): int
    {
        $query = Factura::deVenta()->aceptadas();

        if ($terceroId) {
            $query->where('tercero_id', $terceroId);
        }

        return $query->count();
    }

    /**
     * Obtiene el Total de Clientes únicos
     */
    public function getTotalClientes(?int $terceroId = null): int
    {
        if ($terceroId) {
            // Si hay filtro de tercero, solo contar ese cliente
            return 1;
        }

        return Factura::distinct('tercero_id')->count('tercero_id');
    }

    /**
     * Calcula el Promedio Facturado
     */
    public function getPromedioFacturado(?int $terceroId = null): float
    {
        $query = Factura::deVenta()->aceptadas();

        if ($terceroId) {
            $query->where('tercero_id', $terceroId);
        }

        $cantidad = $query->count();
        if ($cantidad === 0) {
            return 0;
        }

        $suma = $query->sum('total_pagar');
        return round($suma / $cantidad, 2);
    }

    /**
     * Obtiene el Total Recaudado (Facturas Aceptadas)
     */
    public function getTotalRecaudado(?int $terceroId = null): float
    {
        $query = Factura::deVenta()->aceptadas();

        if ($terceroId) {
            $query->where('tercero_id', $terceroId);
        }

        return $query->sum('total_pagar');
    }

    /**
     * Obtiene el Total Pendiente (Facturas Pendientes o Rechazadas)
     */
    public function getTotalPendiente(?int $terceroId = null): float
    {
        $query = Factura::deVenta();

        if ($terceroId) {
            $query->where('tercero_id', $terceroId);
        }

        $pendientes = (clone $query)
            ->where('estado', 'pendiente')
            ->sum('total_pagar');

        $rechazadas = (clone $query)
            ->where('estado', 'rechazada')
            ->sum('total_pagar');

        return $pendientes + $rechazadas;
    }

    /**
     * Obtiene las últimas 5 facturas
     */
    public function getUltimasFacturas(?int $terceroId = null, int $limite = 5): \Illuminate\Database\Eloquent\Collection
    {
        $query = Factura::deVenta()->aceptadas();

        if ($terceroId) {
            $query->where('tercero_id', $terceroId);
        }

        return $query->with('tercero')
            ->orderBy('fecha_factura', 'desc')
            ->limit($limite)
            ->get();
    }

    /**
     * Búsqueda de terceros con autocompletar
     */
    public function buscarTerceros(string $termino, int $limite = 10): \Illuminate\Database\Eloquent\Collection
    {
        return Tercero::where('nombre_razon_social', 'like', "%$termino%")
            ->orWhere('nit', 'like', "%$termino%")
            ->limit($limite)
            ->get(['id', 'nombre_razon_social', 'nit']);
    }

    /**
     * Obtiene datos para gráfico de tendencias (últimos 30 días)
     */
    public function getTendenciaVentas(?int $terceroId = null): array
    {
        $query = Factura::deVenta()->aceptadas();

        if ($terceroId) {
            $query->where('tercero_id', $terceroId);
        }

        $data = $query->selectRaw('DATE(fecha_factura) as fecha, SUM(total_pagar) as total')
            ->where('fecha_factura', '>=', now()->subDays(30))
            ->groupBy('fecha')
            ->orderBy('fecha', 'asc')
            ->get();

        return $data->mapWithKeys(function ($item) {
            return [$item->fecha => $item->total];
        })->toArray();
    }

    /**
     * Obtiene desglose por tipo de documento
     */
    public function getDesgloceDocumentos(?int $terceroId = null): array
    {
        $query = Factura::aceptadas();

        if ($terceroId) {
            $query->where('tercero_id', $terceroId);
        }

        $facturas = (clone $query)->deVenta()->count();
        $notasCredito = (clone $query)->notasCredito()->count();

        return [
            'facturas_venta' => $facturas,
            'notas_credito' => $notasCredito,
            'total_documentos' => $facturas + $notasCredito,
        ];
    }

    /**
     * Obtiene estadísticas por tercero (Top clientes)
     */
    public function getTopClientes(int $limite = 10, ?int $terceroId = null): \Illuminate\Database\Eloquent\Collection
    {
        $query = Factura::deVenta()->aceptadas();

        if ($terceroId) {
            $query->where('tercero_id', $terceroId);
        }

        return $query->selectRaw('tercero_id, COUNT(*) as cantidad, SUM(total_pagar) as total')
            ->groupBy('tercero_id')
            ->orderBy('total', 'desc')
            ->limit($limite)
            ->with('tercero')
            ->get();
    }

    /**
     * Genera clave de caché
     */
    private function getCacheKey(string $tipo, ?int $terceroId = null): string
    {
        $suffix = $terceroId ? "tercero_{$terceroId}" : 'consolidado';
        return "dashboard_{$tipo}_{$suffix}";
    }

    /**
     * Invalida caché (útil cuando se agregan nuevas facturas)
     */
    public static function invalidateCache(?int $terceroId = null): void
    {
        $service = new self();

        $tipos = ['kpis', 'tendencias', 'desgloce', 'top_clientes'];

        foreach ($tipos as $tipo) {
            $cacheKey = $service->getCacheKey($tipo, $terceroId);
            Cache::forget($cacheKey);
        }

        // Si es caché consolidado, también invalida de terceros específicos
        if (!$terceroId) {
            // Invalidar caché global
            Cache::forget('dashboard_kpis_consolidado');
        }
    }
}

