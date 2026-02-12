<?php

namespace App\Livewire;

use App\Models\Tercero;
use App\Models\Factura;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

class TercerosTable extends Component
{
    use WithPagination;

    // Filtros de búsqueda
    public string $search = '';
    public string $nit = '';
    public string $estado = '';
    public ?string $fechaDesde = null;
    public ?string $fechaHasta = null;

    // Ordenamiento
    public string $sortField = 'nombre_razon_social';
    public string $sortDirection = 'asc';

    // Paginación
    public int $perPage = 15;

    // UI State
    public bool $showFilters = false;

    // Merge modal
    public bool $showMergeModal = false;
    public ?int $selectedTerceroId = null;
    public ?int $mergeTerceroId = null;
    public array $selectedTerceros = [];
    public array $mergeOptions = [];

    // Query string para mantener filtros en URL
    protected $queryString = [
        'search' => ['except' => ''],
        'nit' => ['except' => ''],
        'estado' => ['except' => ''],
        'fechaDesde' => ['except' => null],
        'fechaHasta' => ['except' => null],
        'sortField' => ['except' => 'nombre_razon_social'],
        'sortDirection' => ['except' => 'asc'],
        'perPage' => ['except' => 15],
    ];

    // Reset pagination cuando cambian los filtros
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingNit()
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
            'nit',
            'estado',
            'fechaDesde',
            'fechaHasta',
        ]);
        $this->resetPage();
    }

    /**
     * Verificar si hay filtros activos
     */
    public function hasActiveFilters(): bool
    {
        return !empty($this->search) ||
               !empty($this->nit) ||
               !empty($this->estado) ||
               !empty($this->fechaDesde) ||
               !empty($this->fechaHasta);
    }

    /**
     * Contar filtros activos
     */
    public function activeFiltersCount(): int
    {
        $count = 0;
        if (!empty($this->search)) $count++;
        if (!empty($this->nit)) $count++;
        if (!empty($this->estado)) $count++;
        if (!empty($this->fechaDesde)) $count++;
        if (!empty($this->fechaHasta)) $count++;
        return $count;
    }

    /**
     * Toggle selección de tercero para merge
     */
    public function toggleSelection(int $terceroId): void
    {
        if (in_array($terceroId, $this->selectedTerceros)) {
            // Remover y reindexar el array
            $this->selectedTerceros = array_values(
                array_filter($this->selectedTerceros, fn($id) => $id !== $terceroId)
            );
        } else {
            if (count($this->selectedTerceros) < 5) { // Máximo 5 terceros
                $this->selectedTerceros[] = $terceroId;
            }
        }
    }

    /**
     * Abrir modal de merge
     */
    public function openMergeModal(): void
    {
        if (count($this->selectedTerceros) < 2) {
            session()->flash('error', 'Debe seleccionar al menos 2 terceros para fusionar.');
            return;
        }

        $tenantId = session('tenant_id');
        $this->mergeOptions = Tercero::byTenant($tenantId)
            ->whereIn('id', $this->selectedTerceros)
            ->withCount('facturas')
            ->get()
            ->map(function ($tercero) {
                return [
                    'id' => $tercero->id,
                    'nit' => $tercero->nit,
                    'nombre' => $tercero->nombre_razon_social,
                    'facturas_count' => $tercero->facturas_count,
                    'email' => $tercero->email,
                    'telefono' => $tercero->telefono,
                ];
            })
            ->toArray();

        $this->showMergeModal = true;
    }

    /**
     * Cerrar modal de merge
     */
    public function closeMergeModal(): void
    {
        $this->showMergeModal = false;
        $this->mergeTerceroId = null;
        $this->mergeOptions = [];
    }

    /**
     * Ejecutar merge de terceros
     */
    public function executeMerge(): void
    {
        if (!$this->mergeTerceroId) {
            session()->flash('error', 'Debe seleccionar el tercero dominante.');
            return;
        }

        $tenantId = session('tenant_id');

        try {
            DB::beginTransaction();

            // Obtener tercero dominante
            $terceroDominante = Tercero::byTenant($tenantId)->findOrFail($this->mergeTerceroId);

            // Terceros a fusionar (eliminar)
            $tercerosAFusionar = array_filter($this->selectedTerceros, fn($id) => $id !== $this->mergeTerceroId);

            $facturasReasignadas = 0;
            $cantidadFusionados = count($tercerosAFusionar);

            foreach ($tercerosAFusionar as $terceroId) {
                // Reasignar facturas al tercero dominante
                $count = Factura::where('tercero_id', $terceroId)
                    ->where('tenant_id', $tenantId)
                    ->update(['tercero_id' => $terceroDominante->id]);

                $facturasReasignadas += $count;

                // Eliminar tercero fusionado
                Tercero::byTenant($tenantId)->where('id', $terceroId)->delete();
            }

            DB::commit();

            // Resetear TODOS los estados
            $this->showMergeModal = false;
            $this->mergeTerceroId = null;
            $this->mergeOptions = [];
            $this->selectedTerceros = [];

            // Resetear paginación para refrescar la tabla
            $this->resetPage();

            session()->flash('success', sprintf(
                '✅ Fusión completada: %d tercero(s) fusionados en "%s". %d factura(s) reasignadas.',
                $cantidadFusionados,
                $terceroDominante->nombre_razon_social,
                $facturasReasignadas
            ));

        } catch (\Exception $e) {
            DB::rollBack();
            $this->showMergeModal = false;
            session()->flash('error', 'Error al fusionar terceros: ' . $e->getMessage());
        }
    }

    /**
     * Cancelar selección
     */
    public function cancelSelection(): void
    {
        $this->selectedTerceros = [];
    }

    /**
     * Obtener estadísticas de un tercero
     */
    public function getTerceroStats(int $terceroId): array
    {
        $tenantId = session('tenant_id');

        $stats = Factura::where('tercero_id', $terceroId)
            ->where('tenant_id', $tenantId)
            ->selectRaw('
                COUNT(*) as total_facturas,
                SUM(total_pagar) as total_facturado,
                MAX(fecha_factura) as ultima_factura
            ')
            ->first();

        return [
            'total_facturas' => $stats->total_facturas ?? 0,
            'total_facturado' => $stats->total_facturado ?? 0,
            'ultima_factura' => $stats->ultima_factura,
        ];
    }

    /**
     * Renderizar el componente
     */
    public function render()
    {
        $tenantId = session('tenant_id');

        $query = Tercero::byTenant($tenantId)
            ->withCount('facturas')
            ->withSum('facturas', 'total_pagar');

        // Aplicar filtros
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('nombre_razon_social', 'like', '%' . $this->search . '%')
                  ->orWhere('email', 'like', '%' . $this->search . '%');
            });
        }

        if (!empty($this->nit)) {
            $query->where('nit', 'like', '%' . $this->nit . '%');
        }

        if (!empty($this->estado)) {
            $query->where('estado', $this->estado);
        }

        if (!empty($this->fechaDesde)) {
            $query->where('created_at', '>=', $this->fechaDesde . ' 00:00:00');
        }

        if (!empty($this->fechaHasta)) {
            $query->where('created_at', '<=', $this->fechaHasta . ' 23:59:59');
        }

        // Ordenamiento
        $query->orderBy($this->sortField, $this->sortDirection);

        // Estadísticas generales
        $stats = [
            'total' => Tercero::byTenant($tenantId)->count(),
            'activos' => Tercero::byTenant($tenantId)->where('estado', 'activo')->count(),
            'inactivos' => Tercero::byTenant($tenantId)->where('estado', 'inactivo')->count(),
        ];

        return view('livewire.terceros-table', [
            'terceros' => $query->paginate($this->perPage),
            'stats' => $stats,
        ]);
    }
}

