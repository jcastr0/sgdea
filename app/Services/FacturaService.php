<?php

namespace App\Services;

use App\Models\Factura;
use App\Models\SecurityEvent;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class FacturaService
{
    /**
     * Validar si el CUFE ya existe (prevención de duplicados)
     */
    public function validarCufeUnico(string $cufe, ?int $exceptoId = null): array
    {
        $existe = Factura::existeCufe($cufe, $exceptoId);

        return [
            'valido' => !$existe,
            'mensaje' => $existe ? "Ya existe una factura con el CUFE: $cufe" : null,
            'factura_existente' => $existe ? Factura::where('cufe', $cufe)->first() : null,
        ];
    }

    /**
     * Obtener facturas con filtros avanzados
     */
    public function buscarFacturas(array $filtros, int $porPagina = 15)
    {
        $query = Factura::with('tercero');

        // Aplicar búsqueda avanzada
        $query->busquedaAvanzada($filtros);

        // Ordenar por defecto
        $orden = $filtros['orden'] ?? 'fecha_factura';
        $direccion = $filtros['direccion'] ?? 'desc';
        $query->orderBy($orden, $direccion);

        return $query->paginate($porPagina);
    }

    /**
     * Obtener detalles de una factura con auditoría
     */
    public function obtenerFacturaConAuditoria(int $facturaId, int $usuarioId): ?Factura
    {
        $factura = Factura::with('tercero')->find($facturaId);

        if ($factura) {
            // Registrar consulta en auditoría
            SecurityEvent::create([
                'user_id' => $usuarioId,
                'event_type' => 'factura_viewed',
                'description' => "Factura consultada: {$factura->numero_factura} (CUFE: {$factura->cufe})",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => json_encode($factura->getInfoAuditoria()),
            ]);
        }

        return $factura;
    }

    /**
     * Verificar integridad del PDF
     */
    public function verificarIntegridadPdf(Factura $factura): array
    {
        if (!$factura->tienePdf()) {
            return [
                'valido' => false,
                'mensaje' => 'La factura no tiene un PDF asociado',
            ];
        }

        $integro = $factura->verificarIntegridadPdf();

        return [
            'valido' => $integro,
            'mensaje' => $integro
                ? 'El PDF está íntegro (hash coincide)'
                : '⚠️ ADVERTENCIA: El hash del PDF no coincide. El archivo puede haber sido modificado.',
            'hash_almacenado' => $factura->hash_pdf,
            'hash_actual' => $factura->tienePdf()
                ? hash_file('sha256', public_path($factura->pdf_path))
                : null,
        ];
    }

    /**
     * Descargar PDF con auditoría
     */
    public function descargarPdf(Factura $factura, int $usuarioId)
    {
        if (!$factura->tienePdf()) {
            return [
                'success' => false,
                'error' => 'La factura no tiene un PDF asociado',
            ];
        }

        // Registrar descarga en auditoría
        SecurityEvent::create([
            'user_id' => $usuarioId,
            'event_type' => 'factura_pdf_downloaded',
            'description' => "PDF descargado: Factura {$factura->numero_factura} (CUFE: {$factura->cufe})",
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
            'metadata' => json_encode([
                'factura_id' => $factura->id,
                'cufe' => $factura->cufe,
                'numero_factura' => $factura->numero_factura,
                'pdf_path' => $factura->pdf_path,
                'hash_pdf' => $factura->hash_pdf,
            ]),
        ]);

        return [
            'success' => true,
            'ruta' => public_path($factura->pdf_path),
            'nombre_archivo' => basename($factura->pdf_path),
        ];
    }

    /**
     * Actualizar estado de factura con auditoría
     */
    public function actualizarEstado(Factura $factura, string $nuevoEstado, int $usuarioId): array
    {
        $estadoAnterior = $factura->estado;

        try {
            $factura->update(['estado' => $nuevoEstado]);

            // Registrar cambio de estado en auditoría
            SecurityEvent::create([
                'user_id' => $usuarioId,
                'event_type' => 'factura_estado_updated',
                'description' => "Estado actualizado: Factura {$factura->numero_factura} cambió de '{$estadoAnterior}' a '{$nuevoEstado}'",
                'ip_address' => request()->ip(),
                'user_agent' => request()->userAgent(),
                'metadata' => json_encode([
                    'factura_id' => $factura->id,
                    'cufe' => $factura->cufe,
                    'numero_factura' => $factura->numero_factura,
                    'estado_anterior' => $estadoAnterior,
                    'estado_nuevo' => $nuevoEstado,
                ]),
            ]);

            return [
                'success' => true,
                'mensaje' => "Estado actualizado correctamente de '{$estadoAnterior}' a '{$nuevoEstado}'",
            ];
        } catch (\Exception $e) {
            Log::error('Error al actualizar estado de factura: ' . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Error al actualizar el estado. Intente nuevamente.',
            ];
        }
    }

    /**
     * Obtener estadísticas de facturas
     */
    public function obtenerEstadisticas(array $filtros = []): array
    {
        $query = Factura::query();

        // Aplicar filtros si existen
        if (!empty($filtros)) {
            $query->busquedaAvanzada($filtros);
        }

        // Montos por tipo de documento
        $totalFacturado = (clone $query)->deVenta()->aceptadas()->sum('total_pagar');
        $totalNotasDebito = (clone $query)->notasDebito()->aceptadas()->sum('total_pagar');
        $totalNotasCredito = (clone $query)->notasCredito()->aceptadas()->sum('total_pagar');

        // Total Neto = Facturas + Notas Débito - Notas Crédito
        $totalFacturadoNeto = $totalFacturado + $totalNotasDebito - $totalNotasCredito;

        return [
            'total_facturas' => (clone $query)->count(),
            'total_facturas_venta' => (clone $query)->deVenta()->count(),
            'total_notas_credito' => (clone $query)->notasCredito()->count(),
            'total_notas_debito' => (clone $query)->notasDebito()->count(),
            'facturas_aceptadas' => (clone $query)->aceptadas()->count(),
            'facturas_rechazadas' => (clone $query)->where('estado', 'rechazada')->count(),
            'facturas_pendientes' => (clone $query)->where('estado', 'pendiente')->count(),
            'con_pdf' => (clone $query)->conPdf()->count(),
            'sin_pdf' => (clone $query)->sinPdf()->count(),
            'total_facturado' => $totalFacturado,
            'total_notas_debito_monto' => $totalNotasDebito,
            'total_notas_credito_monto' => $totalNotasCredito,
            'total_facturado_neto' => max(0, $totalFacturadoNeto), // Total real (nunca negativo)
        ];
    }

    /**
     * Buscar facturas con autocompletar
     */
    public function buscarConAutocompletar(string $termino, int $limite = 10)
    {
        return Factura::with('tercero')
            ->buscar($termino)
            ->limit($limite)
            ->get(['id', 'numero_factura', 'cufe', 'tercero_id', 'fecha_factura', 'total_pagar', 'estado']);
    }

    /**
     * Exportar facturas a Excel (preparación)
     */
    public function prepararDatosExportacion(array $filtros): array
    {
        $facturas = Factura::with('tercero')
            ->busquedaAvanzada($filtros)
            ->orderBy('fecha_factura', 'desc')
            ->get();

        return $facturas->map(function ($factura) {
            return [
                'Número Factura' => $factura->numero_factura,
                'CUFE' => $factura->cufe,
                'Tipo Documento' => $factura->tipo_documento,
                'Cliente' => $factura->tercero?->nombre_razon_social,
                'NIT Cliente' => $factura->tercero?->nit,
                'Fecha Emisión' => $factura->fecha_factura->format('d/m/Y H:i'),
                'Fecha Vencimiento' => $factura->fecha_vencimiento?->format('d/m/Y'),
                'Subtotal' => $factura->subtotal,
                'Impuestos' => $factura->total_impuestos,
                'Retenciones' => $factura->total_retenciones,
                'Total a Pagar' => $factura->total_pagar,
                'Estado' => ucfirst($factura->estado),
                'Motonave' => $factura->motonave,
                'TRB' => $factura->trb,
                'Servicio' => $factura->servicio_suministrado,
                'Locación' => $factura->locacion,
                'Tiene PDF' => $factura->tienePdf() ? 'Sí' : 'No',
            ];
        })->toArray();
    }

    /**
     * Obtener valores únicos para filtros (autocompletar)
     */
    public function obtenerValoresUnicos(string $campo): array
    {
        $valores = Factura::query()
            ->whereNotNull($campo)
            ->where($campo, '!=', '')
            ->distinct()
            ->pluck($campo)
            ->filter()
            ->sort()
            ->values()
            ->toArray();

        return $valores;
    }
}

