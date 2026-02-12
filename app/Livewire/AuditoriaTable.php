<?php

namespace App\Livewire;

use App\Models\AuditLog;
use App\Models\User;
use Livewire\Component;
use Livewire\WithPagination;

class AuditoriaTable extends Component
{
    use WithPagination;

    // Filtros
    public string $search = '';
    public string $action = '';
    public string $userId = '';
    public string $modelType = '';
    public string $fechaDesde = '';
    public string $fechaHasta = '';

    // UI
    public bool $showFilters = false;
    public int $perPage = 15;
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // Query string para mantener filtros en URL
    protected $queryString = [
        'search' => ['except' => ''],
        'action' => ['except' => ''],
        'userId' => ['except' => ''],
        'modelType' => ['except' => ''],
        'fechaDesde' => ['except' => ''],
        'fechaHasta' => ['except' => ''],
        'perPage' => ['except' => 15],
    ];

    public function mount()
    {
        // Si hay filtros activos, mostrar panel
        if ($this->hasActiveFilters()) {
            $this->showFilters = true;
        }
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingAction()
    {
        $this->resetPage();
    }

    public function updatingUserId()
    {
        $this->resetPage();
    }

    public function updatingModelType()
    {
        $this->resetPage();
    }

    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    public function clearFilters()
    {
        $this->reset(['search', 'action', 'userId', 'modelType', 'fechaDesde', 'fechaHasta']);
        $this->resetPage();
    }

    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    public function hasActiveFilters(): bool
    {
        return !empty($this->search) ||
               !empty($this->action) ||
               !empty($this->userId) ||
               !empty($this->modelType) ||
               !empty($this->fechaDesde) ||
               !empty($this->fechaHasta);
    }

    public function activeFiltersCount(): int
    {
        $count = 0;
        if (!empty($this->search)) $count++;
        if (!empty($this->action)) $count++;
        if (!empty($this->userId)) $count++;
        if (!empty($this->modelType)) $count++;
        if (!empty($this->fechaDesde)) $count++;
        if (!empty($this->fechaHasta)) $count++;
        return $count;
    }

    public function getCanFilterByUserProperty(): bool
    {
        $user = auth()->user();
        return $user && ($user->isAdminTenant() || $user->isSuperadminGlobal());
    }

    public function render()
    {
        $user = auth()->user();
        $tenantId = $user->tenant_id;

        // Query base
        $query = AuditLog::with('user')
            ->orderBy($this->sortField, $this->sortDirection);

        // Filtrar por tenant (usuarios del tenant)
        if ($tenantId && !$user->isSuperadminGlobal()) {
            $userIds = User::where('tenant_id', $tenantId)->pluck('id')->toArray();
            // Incluir usuario SYSTEM (id=1) para ver acciones del sistema
            $userIds[] = 1;
            $query->whereIn('user_id', $userIds);

            // Si no es admin, solo ver sus propias acciones
            if (!$user->isAdminTenant()) {
                $query->where('user_id', $user->id);
            }
        }

        // Aplicar filtros
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->whereHas('user', function ($uq) {
                    $uq->where('name', 'like', "%{$this->search}%")
                       ->orWhere('email', 'like', "%{$this->search}%");
                })
                ->orWhere('ip_address', 'like', "%{$this->search}%");
            });
        }

        if (!empty($this->action)) {
            $query->where('action', $this->action);
        }

        if (!empty($this->userId)) {
            $query->where('user_id', $this->userId);
        }

        if (!empty($this->modelType)) {
            $query->where('model_type', 'like', "%{$this->modelType}%");
        }

        if (!empty($this->fechaDesde)) {
            $query->whereDate('created_at', '>=', $this->fechaDesde);
        }

        if (!empty($this->fechaHasta)) {
            $query->whereDate('created_at', '<=', $this->fechaHasta);
        }

        $logs = $query->paginate($this->perPage);

        // Obtener acciones únicas para el filtro
        $acciones = AuditLog::select('action')
            ->distinct()
            ->orderBy('action')
            ->pluck('action');

        // Obtener usuarios para filtro (solo si tiene permisos)
        $usuarios = collect();
        if ($this->canFilterByUser) {
            if ($tenantId && !$user->isSuperadminGlobal()) {
                $usuarios = User::where('tenant_id', $tenantId)
                    ->orderBy('name')
                    ->get(['id', 'name']);
            } else {
                $usuarios = User::orderBy('name')->get(['id', 'name']);
            }
        }

        // Estadísticas
        $stats = [
            'total' => $logs->total(),
            'acciones_count' => $acciones->count(),
            'usuarios_count' => $usuarios->count(),
            'ultima_accion' => $logs->first()?->created_at?->diffForHumans() ?? '-',
        ];

        return view('livewire.auditoria-table', [
            'logs' => $logs,
            'acciones' => $acciones,
            'usuarios' => $usuarios,
            'stats' => $stats,
            'canFilterByUser' => $this->canFilterByUser,
        ]);
    }
}

