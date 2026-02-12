<?php

namespace App\Livewire;

use App\Models\Factura;
use App\Models\Tercero;
use Livewire\Component;
use Livewire\WithPagination;

class FacturasTable extends Component
{
    use WithPagination;

    // Filtros de búsqueda
    public string $search = '';
    public string $numeroFactura = '';
    public string $cufe = '';
    public string $terceroSearch = '';
    public ?int $terceroId = null;
    public string $motonave = '';
    public string $trb = '';
    public ?string $fechaDesde = null;
    public ?string $fechaHasta = null;
    public ?float $totalMin = null;
    public ?float $totalMax = null;
    public string $estado = '';
    public string $tienePdf = '';

    // Ordenamiento
    public string $sortField = 'fecha_factura';
    public string $sortDirection = 'desc';

    // Paginación
    public int $perPage = 15;

    // UI State
    public bool $showFilters = false;

    // Query string para mantener filtros en URL
    protected $queryString = [
        'search' => ['except' => ''],
        'numeroFactura' => ['except' => ''],
        'cufe' => ['except' => ''],
        'terceroId' => ['except' => null],
        'estado' => ['except' => ''],
        'fechaDesde' => ['except' => null],
        'fechaHasta' => ['except' => null],
        'sortField' => ['except' => 'fecha_factura'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 15],
    ];

    // Reset pagination cuando cambian los filtros
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingNumeroFactura()
    {
        $this->resetPage();
    }

    public function updatingCufe()
    {
        $this->resetPage();
    }

    public function updatingTerceroId()
    {
        $this->resetPage();
    }

    public function updatingEstado()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    /**
     * Ordenar por columna
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Toggle panel de filtros avanzados
     */
    public function toggleFilters(): void
    {
        $this->showFilters = !$this->showFilters;
    }

    /**
     * Limpiar todos los filtros
     */
    public function clearFilters(): void
    {
        $this->reset([
            'search',
            'numeroFactura',
            'cufe',
            'terceroSearch',
            'terceroId',
            'motonave',
            'trb',
            'fechaDesde',
            'fechaHasta',
            'totalMin',
            'totalMax',
            'estado',
            'tienePdf',
        ]);
        $this->resetPage();
    }

    /**
     * Verificar si hay filtros activos
     */
    public function hasActiveFilters(): bool
    {
        return !empty($this->search) ||
               !empty($this->numeroFactura) ||
               !empty($this->cufe) ||
               !empty($this->terceroId) ||
               !empty($this->estado) ||
               !empty($this->fechaDesde) ||
               !empty($this->fechaHasta) ||
               !empty($this->totalMin) ||
               !empty($this->totalMax) ||
               !empty($this->motonave) ||
               !empty($this->trb) ||
               !empty($this->tienePdf);
    }

    /**
     * Contar filtros activos
     */
    public function activeFiltersCount(): int
    {
        $count = 0;
        if (!empty($this->search)) $count++;
        if (!empty($this->numeroFactura)) $count++;
        if (!empty($this->cufe)) $count++;
        if (!empty($this->terceroId)) $count++;
        if (!empty($this->estado)) $count++;
        if (!empty($this->fechaDesde)) $count++;
        if (!empty($this->fechaHasta)) $count++;
        if (!empty($this->totalMin)) $count++;
        if (!empty($this->totalMax)) $count++;
        if (!empty($this->motonave)) $count++;
        if (!empty($this->trb)) $count++;
        if (!empty($this->tienePdf)) $count++;
        return $count;
    }

    /**
     * Obtener estadísticas
     */
    public function getEstadisticasProperty(): array
    {
        $tenantId = session('tenant_id');
        $query = $this->buildQuery();

        $total = $query->count();
        $sumaTotalPagar = $query->sum('total_pagar');
        $promedio = $total > 0 ? $sumaTotalPagar / $total : 0;

        // Contar por estado (sin filtros)
        $porEstado = Factura::byTenant($tenantId)
            ->selectRaw('estado, COUNT(*) as total')
            ->groupBy('estado')
            ->pluck('total', 'estado')
            ->toArray();

        return [
            'total' => $total,
            'suma_total_pagar' => $sumaTotalPagar,
            'promedio_por_factura' => $promedio,
            'por_estado' => $porEstado,
        ];
    }

    /**
     * Obtener terceros para el filtro
     */
    public function getTercerosProperty()
    {
        $tenantId = session('tenant_id');
        return Tercero::byTenant($tenantId)->orderBy('nombre_razon_social')->get();
    }

    /**
     * Construir query base con filtros
     */
    protected function buildQuery()
    {
        $tenantId = session('tenant_id');

        $query = Factura::byTenant($tenantId)->with('tercero');

        // Búsqueda rápida
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('numero_factura', 'like', "%{$this->search}%")
                  ->orWhere('cufe', 'like', "%{$this->search}%")
                  ->orWhereHas('tercero', function ($tq) {
                      $tq->where('nombre_razon_social', 'like', "%{$this->search}%")
                        ->orWhere('nit', 'like', "%{$this->search}%");
                  });
            });
        }

        // Filtros específicos
        if (!empty($this->numeroFactura)) {
            $query->where('numero_factura', 'like', "%{$this->numeroFactura}%");
        }

        if (!empty($this->cufe)) {
            $query->where('cufe', 'like', "%{$this->cufe}%");
        }

        if (!empty($this->terceroId)) {
            $query->where('tercero_id', $this->terceroId);
        }

        if (!empty($this->estado)) {
            $query->where('estado', $this->estado);
        }

        if (!empty($this->fechaDesde)) {
            $query->whereDate('fecha_factura', '>=', $this->fechaDesde);
        }

        if (!empty($this->fechaHasta)) {
            $query->whereDate('fecha_factura', '<=', $this->fechaHasta);
        }

        if (!empty($this->totalMin)) {
            $query->where('total_pagar', '>=', $this->totalMin);
        }

        if (!empty($this->totalMax)) {
            $query->where('total_pagar', '<=', $this->totalMax);
        }

        if (!empty($this->motonave)) {
            $query->where('motonave', 'like', "%{$this->motonave}%");
        }

        if (!empty($this->trb)) {
            $query->where('trb', 'like', "%{$this->trb}%");
        }

        if ($this->tienePdf !== '') {
            if ($this->tienePdf === '1') {
                $query->whereNotNull('pdf_path')->where('pdf_path', '!=', '');
            } else {
                $query->where(function ($q) {
                    $q->whereNull('pdf_path')->orWhere('pdf_path', '=', '');
                });
            }
        }

        return $query;
    }

    /**
     * Render del componente
     */
    public function render()
    {
        $query = $this->buildQuery();

        // Aplicar ordenamiento
        $query->orderBy($this->sortField, $this->sortDirection);

        $facturas = $query->paginate($this->perPage);

        return view('livewire.facturas-table', [
            'facturas' => $facturas,
            'estadisticas' => $this->estadisticas,
            'terceros' => $this->terceros,
        ]);
    }
}

