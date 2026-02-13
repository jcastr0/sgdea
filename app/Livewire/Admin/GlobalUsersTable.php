<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * ============================================================================
 * LIVEWIRE: GlobalUsersTable
 * ============================================================================
 *
 * Componente Livewire para el listado global de usuarios del sistema.
 * Permite filtrar, ordenar y realizar acciones sobre usuarios de todos los tenants.
 *
 * @author SGDEA Team
 * ============================================================================
 */
class GlobalUsersTable extends Component
{
    use WithPagination;

    // ===========================
    // FILTROS
    // ===========================
    public string $search = '';
    public string $tenantId = '';
    public string $roleId = '';
    public string $status = '';
    public string $fechaDesde = '';
    public string $fechaHasta = '';

    // ===========================
    // ORDENAMIENTO
    // ===========================
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // ===========================
    // UI STATE
    // ===========================
    public bool $showFilters = false;
    public int $perPage = 15;

    // ===========================
    // MODALS
    // ===========================
    public bool $showResetPasswordModal = false;
    public bool $showDeleteModal = false;
    public bool $showChangeRoleModal = false;
    public bool $showChangeTenantModal = false;
    public ?int $selectedUserId = null;
    public ?int $newRoleId = null;
    public ?int $newTenantId = null;
    public ?string $generatedPassword = null;

    // ===========================
    // QUERY STRING
    // ===========================
    protected $queryString = [
        'search' => ['except' => ''],
        'tenantId' => ['except' => ''],
        'roleId' => ['except' => ''],
        'status' => ['except' => ''],
        'sortField' => ['except' => 'created_at'],
        'sortDirection' => ['except' => 'desc'],
    ];

    // ===========================
    // LISTENERS
    // ===========================
    protected $listeners = ['refreshUsers' => '$refresh'];

    /**
     * Reset pagination cuando cambian los filtros
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatingTenantId()
    {
        $this->resetPage();
    }

    public function updatingRoleId()
    {
        $this->resetPage();
    }

    public function updatingStatus()
    {
        $this->resetPage();
    }

    /**
     * Ordenar por columna
     */
    public function sortBy(string $field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Toggle panel de filtros
     */
    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    /**
     * Limpiar todos los filtros
     */
    public function clearFilters()
    {
        $this->reset(['search', 'tenantId', 'roleId', 'status', 'fechaDesde', 'fechaHasta']);
        $this->resetPage();
    }

    /**
     * Verificar si hay filtros activos
     */
    public function hasActiveFilters(): bool
    {
        return !empty($this->search)
            || !empty($this->tenantId)
            || !empty($this->roleId)
            || !empty($this->status)
            || !empty($this->fechaDesde)
            || !empty($this->fechaHasta);
    }

    /**
     * Contar filtros activos
     */
    public function activeFiltersCount(): int
    {
        $count = 0;
        if (!empty($this->search)) $count++;
        if (!empty($this->tenantId)) $count++;
        if (!empty($this->roleId)) $count++;
        if (!empty($this->status)) $count++;
        if (!empty($this->fechaDesde)) $count++;
        if (!empty($this->fechaHasta)) $count++;
        return $count;
    }

    // ===========================
    // ACCIONES
    // ===========================

    /**
     * Abrir modal de reset password
     */
    public function openResetPasswordModal(int $userId)
    {
        $this->selectedUserId = $userId;
        $this->generatedPassword = null;
        $this->showResetPasswordModal = true;
    }

    /**
     * Ejecutar reset de password
     */
    public function executeResetPassword()
    {
        $user = User::find($this->selectedUserId);
        if (!$user) {
            session()->flash('error', 'Usuario no encontrado.');
            $this->closeModals();
            return;
        }

        $this->generatedPassword = Str::random(12);
        $user->update(['password' => Hash::make($this->generatedPassword)]);

        session()->flash('success', "Contraseña de '{$user->name}' reseteada correctamente.");
    }

    /**
     * Abrir modal de eliminar
     */
    public function openDeleteModal(int $userId)
    {
        $this->selectedUserId = $userId;
        $this->showDeleteModal = true;
    }

    /**
     * Ejecutar eliminación
     */
    public function executeDelete()
    {
        $user = User::find($this->selectedUserId);
        if (!$user) {
            session()->flash('error', 'Usuario no encontrado.');
            $this->closeModals();
            return;
        }

        // No permitir eliminar superadmins ni usuario SYSTEM
        if ($user->role && $user->role->slug === 'super_admin') {
            session()->flash('error', 'No se puede eliminar un Superadmin Global.');
            $this->closeModals();
            return;
        }

        if ($user->id === 1) {
            session()->flash('error', 'No se puede eliminar el usuario SYSTEM.');
            $this->closeModals();
            return;
        }

        $userName = $user->name;
        $user->delete();

        session()->flash('success', "Usuario '{$userName}' eliminado correctamente.");
        $this->closeModals();
    }

    /**
     * Toggle estado del usuario
     */
    public function toggleStatus(int $userId)
    {
        $user = User::find($userId);
        if (!$user) {
            session()->flash('error', 'Usuario no encontrado.');
            return;
        }

        // No permitir cambiar estado de superadmins
        if ($user->role && $user->role->slug === 'super_admin') {
            session()->flash('error', 'No se puede modificar el estado de un Superadmin Global.');
            return;
        }

        $newStatus = $user->status === 'active' ? 'blocked' : 'active';
        $user->update(['status' => $newStatus]);

        $message = $newStatus === 'active'
            ? "Usuario '{$user->name}' activado."
            : "Usuario '{$user->name}' suspendido.";

        session()->flash('success', $message);
    }

    /**
     * Abrir modal de cambiar rol
     */
    public function openChangeRoleModal(int $userId)
    {
        $user = User::find($userId);
        $this->selectedUserId = $userId;
        $this->newRoleId = $user->role_id;
        $this->showChangeRoleModal = true;
    }

    /**
     * Ejecutar cambio de rol
     */
    public function executeChangeRole()
    {
        $user = User::find($this->selectedUserId);
        if (!$user || !$this->newRoleId) {
            session()->flash('error', 'Datos inválidos.');
            $this->closeModals();
            return;
        }

        $role = Role::find($this->newRoleId);
        $user->update(['role_id' => $this->newRoleId]);

        session()->flash('success', "Rol de '{$user->name}' cambiado a '{$role->name}'.");
        $this->closeModals();
    }

    /**
     * Abrir modal de cambiar tenant
     */
    public function openChangeTenantModal(int $userId)
    {
        $user = User::find($userId);
        $this->selectedUserId = $userId;
        $this->newTenantId = $user->tenant_id;
        $this->showChangeTenantModal = true;
    }

    /**
     * Ejecutar cambio de tenant
     */
    public function executeChangeTenant()
    {
        $user = User::find($this->selectedUserId);
        if (!$user || !$this->newTenantId) {
            session()->flash('error', 'Datos inválidos.');
            $this->closeModals();
            return;
        }

        $tenant = Tenant::find($this->newTenantId);

        // Buscar un rol administrador en el nuevo tenant
        $adminRole = Role::where('tenant_id', $this->newTenantId)
            ->where('slug', 'administrador')
            ->first();

        $user->update([
            'tenant_id' => $this->newTenantId,
            'role_id' => $adminRole?->id ?? $user->role_id,
        ]);

        session()->flash('success', "Usuario '{$user->name}' movido a '{$tenant->name}'.");
        $this->closeModals();
    }

    /**
     * Cerrar todos los modals
     */
    public function closeModals()
    {
        $this->showResetPasswordModal = false;
        $this->showDeleteModal = false;
        $this->showChangeRoleModal = false;
        $this->showChangeTenantModal = false;
        $this->selectedUserId = null;
        $this->newRoleId = null;
        $this->newTenantId = null;
        $this->generatedPassword = null;
    }

    /**
     * Obtener estadísticas
     */
    public function getStatsProperty(): array
    {
        return [
            'total' => User::count(),
            'active' => User::where('status', 'active')->count(),
            'pending' => User::where('status', 'pending_approval')->count(),
            'blocked' => User::where('status', 'blocked')->count(),
        ];
    }

    /**
     * Render del componente
     */
    public function render()
    {
        // Query base - excluir usuario SYSTEM (id=1)
        $query = User::with(['tenant', 'role'])
            ->where('id', '>', 1);

        // Filtro de búsqueda
        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('name', 'like', "%{$this->search}%")
                  ->orWhere('email', 'like', "%{$this->search}%");
            });
        }

        // Filtro por tenant
        if (!empty($this->tenantId)) {
            $query->where('tenant_id', $this->tenantId);
        }

        // Filtro por rol
        if (!empty($this->roleId)) {
            $query->where('role_id', $this->roleId);
        }

        // Filtro por estado
        if (!empty($this->status)) {
            $query->where('status', $this->status);
        }

        // Filtro por fecha desde
        if (!empty($this->fechaDesde)) {
            $query->whereDate('created_at', '>=', $this->fechaDesde);
        }

        // Filtro por fecha hasta
        if (!empty($this->fechaHasta)) {
            $query->whereDate('created_at', '<=', $this->fechaHasta);
        }

        // Ordenamiento
        $query->orderBy($this->sortField, $this->sortDirection);

        // Paginación
        $users = $query->paginate($this->perPage);

        // Datos para filtros
        $tenants = Tenant::orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('livewire.admin.global-users-table', [
            'users' => $users,
            'tenants' => $tenants,
            'roles' => $roles,
            'stats' => $this->stats,
        ]);
    }
}

