<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Factura extends Model
{
    use Auditable;

    protected $table = 'facturas';

    protected $fillable = [
        'tenant_id',
        'tercero_id',
        'cufe',
        'numero_factura',
        'fecha_factura',
        'fecha_vencimiento',
        'subtotal',
        'iva',
        'descuento',
        'total_pagar',
        'motonave',
        'trb',
        'servicio_descripcion',
        'pdf_path',
        'hash_pdf',
        'estado',
    ];

    protected $casts = [
        'fecha_factura' => 'datetime',
        'fecha_vencimiento' => 'date',
        'subtotal' => 'decimal:2',
        'iva' => 'decimal:2',
        'descuento' => 'decimal:2',
        'total_pagar' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación: Una factura pertenece a un tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relación: Una factura pertenece a un tercero (cliente)
     */
    public function tercero(): BelongsTo
    {
        return $this->belongsTo(Tercero::class);
    }

    /**
     * Scope: Filtrar por tenant
     */
    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope: Filtrar por rango de fechas
     */
    public function scopeByDateRange($query, $start, $end)
    {
        return $query->whereBetween('fecha_factura', [$start . ' 00:00:00', $end . ' 23:59:59']);
    }

    /**
     * Scope: Filtrar por tercero (cliente)
     */
    public function scopeByThirdParty($query, $terceroId)
    {
        return $query->where('tercero_id', $terceroId);
    }

    /**
     * Scope: Filtrar por estado
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('estado', $status);
    }

    /**
     * Scope: Búsqueda por número de factura o CUFE
     */
    public function scopeSearchByNumber($query, $searchTerm)
    {
        return $query->where('numero_factura', 'like', "%{$searchTerm}%")
                     ->orWhere('cufe', 'like', "%{$searchTerm}%");
    }

    /**
     * Scope: Búsqueda por nombre de tercero
     */
    public function scopeSearchByThirdParty($query, $searchTerm)
    {
        return $query->whereHas('tercero', function ($q) use ($searchTerm) {
            $q->where('nombre_razon_social', 'like', "%{$searchTerm}%")
              ->orWhere('nit', 'like', "%{$searchTerm}%");
        });
    }

    /**
     * Scope: Búsqueda por motonave
     */
    public function scopeSearchByMotonave($query, $searchTerm)
    {
        return $query->where('motonave', 'like', "%{$searchTerm}%");
    }

    /**
     * Scope: Búsqueda por TRB
     */
    public function scopeSearchByTrb($query, $searchTerm)
    {
        return $query->where('trb', 'like', "%{$searchTerm}%");
    }

    /**
     * Scope: Búsqueda por rango de monto total
     */
    public function scopeByTotalRange($query, $minimo, $maximo)
    {
        return $query->whereBetween('total_pagar', [$minimo, $maximo]);
    }

    /**
     * Scope: Búsqueda por rango de subtotal
     */
    public function scopeBySubtotalRange($query, $minimo, $maximo)
    {
        return $query->whereBetween('subtotal', [$minimo, $maximo]);
    }

    /**
     * Scope: Búsqueda avanzada (combina múltiples criterios)
     */
    public function scopeAdvancedSearch($query, $filters = [])
    {
        // Número de factura
        if (!empty($filters['numero_factura'])) {
            $query->where('numero_factura', 'like', "%{$filters['numero_factura']}%");
        }

        // CUFE
        if (!empty($filters['cufe'])) {
            $query->where('cufe', 'like', "%{$filters['cufe']}%");
        }

        // Tercero por nombre o NIT
        if (!empty($filters['tercero_search'])) {
            $query->whereHas('tercero', function ($q) use ($filters) {
                $q->where('nombre_razon_social', 'like', "%{$filters['tercero_search']}%")
                  ->orWhere('nit', 'like', "%{$filters['tercero_search']}%");
            });
        }

        // Tercero por ID
        if (!empty($filters['tercero_id'])) {
            $query->where('tercero_id', $filters['tercero_id']);
        }

        // Motonave
        if (!empty($filters['motonave'])) {
            $query->where('motonave', 'like', "%{$filters['motonave']}%");
        }

        // TRB
        if (!empty($filters['trb'])) {
            $query->where('trb', 'like', "%{$filters['trb']}%");
        }

        // Rango de fechas
        if (!empty($filters['fecha_desde']) && !empty($filters['fecha_hasta'])) {
            $query->whereBetween('fecha_factura', [
                $filters['fecha_desde'] . ' 00:00:00',
                $filters['fecha_hasta'] . ' 23:59:59'
            ]);
        }

        // Rango de montos
        if (!empty($filters['total_min']) && !empty($filters['total_max'])) {
            $query->whereBetween('total_pagar', [
                (float)$filters['total_min'],
                (float)$filters['total_max']
            ]);
        }

        // Estado
        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        // Con PDF o sin PDF
        if (isset($filters['tiene_pdf'])) {
            if ($filters['tiene_pdf']) {
                $query->whereNotNull('pdf_path');
            } else {
                $query->whereNull('pdf_path');
            }
        }

        return $query;
    }

    /**
     * Obtener nombre del estado con colores
     */
    public function getEstadoBadge()
    {
        $estados = [
            'pendiente' => ['clase' => 'badge-warning', 'texto' => 'Pendiente'],
            'pagada' => ['clase' => 'badge-success', 'texto' => 'Pagada'],
            'cancelada' => ['clase' => 'badge-secondary', 'texto' => 'Cancelada'],
        ];

        return $estados[$this->estado] ?? ['clase' => 'badge-secondary', 'texto' => 'Desconocido'];
    }

    /**
     * Calcular total automáticamente
     */
    public function calcularTotal()
    {
        $this->total_pagar = ($this->subtotal + $this->iva) - $this->descuento;
        return $this->total_pagar;
    }

    /**
     * Verificar si tiene PDF
     */
    public function tienePdf(): bool
    {
        return !empty($this->pdf_path) && file_exists(storage_path('app/' . $this->pdf_path));
    }

    /**
     * Obtener ruta pública del PDF
     */
    public function getPdfUrl()
    {
        return $this->tienePdf() ? asset('storage/' . $this->pdf_path) : null;
    }

    /**
     * Generar hash del PDF para verificación
     */
    public static function generarHashPdf($filePath)
    {
        if (file_exists($filePath)) {
            return hash('sha256', file_get_contents($filePath));
        }
        return null;
    }

    /**
     * Verificar integridad del PDF
     */
    public function verificarIntegridadPdf(): bool
    {
        if (!$this->tienePdf() || !$this->hash_pdf) {
            return false;
        }

        $hashActual = self::generarHashPdf(storage_path('app/' . $this->pdf_path));
        return $hashActual === $this->hash_pdf;
    }
}

