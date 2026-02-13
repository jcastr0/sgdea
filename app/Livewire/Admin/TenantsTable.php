<?php

namespace App\Livewire\Admin;

use App\Models\Tenant;
use App\Models\User;
use App\Models\Factura;
use Livewire\Component;
use Livewire\WithPagination;
use Illuminate\Support\Facades\DB;

/**
 * ============================================================================
 * COMPONENTE LIVEWIRE: TenantsTable
 * ============================================================================
 *
 * Componente para listado de tenants con filtros, ordenamiento y acciones.
 * Solo accesible por superadmin global.
 *
 * @author SGDEA Team
 * ============================================================================
 */
class TenantsTable extends Component
{
    use WithPagination;

    // ===========================
    // FILTROS
    // ===========================

    public string $search = '';
    public string $status = '';
    public string $plan = '';
    public ?string $fechaDesde = null;
    public ?string $fechaHasta = null;

    // ===========================
    // ORDENAMIENTO
    // ===========================

    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // ===========================
    // PAGINACIÓN
    // ===========================

    public int $perPage = 15;

    // ===========================
    // UI STATE
    // ===========================

    public bool $showFilters = false;
    public bool $isLoading = false;

    // ===========================
    // QUERY STRING
    // ===========================

    protected $queryString = [
        'search' => ['except' => ''],
        'status' => ['except' => ''],
        'plan' => ['except' => ''],
        'fechaDesde' => ['except' => null],
        'fechaHasta' => ['except' => null],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
        'perPage' => ['except' => 15],
    ];

    // ===========================
    // LISTENERS
    // ===========================

    protected $listeners = [
        'tenantUpdated' => '$refresh',
        'tenantDeleted' => '$refresh',
    ];

    // ===========================
    // RESET PAGINATION
    // ===========================

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    public function updatingPlan()
    {
        $this->resetPage();
    }

    public function updatingPerPage()
    {
        $this->resetPage();
    }

    // ===========================
    // MÉTODOS DE ORDENAMIENTO
    // ===========================

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

    // ===========================
    // MÉTODOS DE FILTROS
    // ===========================

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
            'status',
            'plan',
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
               !empty($this->status) ||
               !empty($this->plan) ||
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
        if (!empty($this->status)) $count++;
        if (!empty($this->plan)) $count++;
        if (!empty($this->fechaDesde)) $count++;
        if (!empty($this->fechaHasta)) $count++;
        return $count;
    }

    // ===========================
    // ACCIONES RÁPIDAS
    // ===========================

    /**
     * Cambiar estado del tenant (activar/suspender)
     */
    public function toggleStatus(int $tenantId): void
    {
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            session()->flash('error', 'Tenant no encontrado');
            return;
        }

        $newStatus = $tenant->status === 'active' ? 'suspended' : 'active';
        $tenant->update(['status' => $newStatus]);

        $message = $newStatus === 'active'
            ? "Tenant '{$tenant->name}' activado correctamente"
            : "Tenant '{$tenant->name}' suspendido correctamente";

        session()->flash('success', $message);
        $this->dispatch('tenantUpdated');
    }

    /**
     * Eliminar tenant
     */
    public function deleteTenant(int $tenantId): void
    {
        $tenant = Tenant::find($tenantId);

        if (!$tenant) {
            session()->flash('error', 'Tenant no encontrado');
            return;
        }

        // Verificar que no tenga datos críticos
        $usersCount = User::where('tenant_id', $tenantId)->count();
        $facturasCount = Factura::where('tenant_id', $tenantId)->count();

        if ($usersCount > 0 || $facturasCount > 0) {
            session()->flash('error', "No se puede eliminar '{$tenant->name}'. Tiene {$usersCount} usuarios y {$facturasCount} facturas asociadas.");
            return;
        }

        $tenantName = $tenant->name;
        $tenant->delete();

        session()->flash('success', "Tenant '{$tenantName}' eliminado correctamente");
        $this->dispatch('tenantDeleted');
    }

    // ===========================
    // ESTADÍSTICAS
    // ===========================

    /**
     * Obtener estadísticas rápidas
     */
    public function getStats(): array
    {
        return [
            'total' => Tenant::count(),
            'active' => Tenant::where('status', 'active')->count(),
            'suspended' => Tenant::where('status', 'suspended')->count(),
            'trial' => Tenant::where('status', 'trial')->count(),
        ];
    }

    // ===========================
    // RENDER
    // ===========================

    public function render()
    {
        // Query base
        $query = Tenant::query()
            ->withCount(['users', 'facturas']);

        // Aplicar filtro de búsqueda
        if (!empty($this->search)) {
            $search = '%' . $this->search . '%';
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', $search)
                  ->orWhere('domain', 'like', $search)
                  ->orWhere('slug', 'like', $search);
            });
        }

        // Filtro por estado
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        // Filtro por plan (si existe el campo)
        // Por ahora no hay campo plan en la tabla, se deja preparado
        // if (!empty($this->plan)) {
        //     $query->where('plan', $this->plan);
        // }

        // Filtro por fecha de creación
        if (!empty($this->fechaDesde)) {
            $query->whereDate('created_at', '>=', $this->fechaDesde);
        }

        if (!empty($this->fechaHasta)) {
            $query->whereDate('created_at', '<=', $this->fechaHasta);
        }

        // Ordenamiento
        $allowedSortFields = ['name', 'created_at', 'status', 'users_count', 'facturas_count'];
        $sortField = in_array($this->sortField, $allowedSortFields) ? $this->sortField : 'created_at';
        $query->orderBy($sortField, $this->sortDirection);

        // Paginación
        $tenants = $query->paginate($this->perPage);

        // Obtener estadísticas
        $stats = $this->getStats();

        return view('livewire.admin.tenants-table', [
            'tenants' => $tenants,
            'stats' => $stats,
        ]);
    }
}

