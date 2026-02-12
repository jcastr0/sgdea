<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Services\AuditService;
use Illuminate\Support\Str;

class RoleController extends Controller
{
    // Middleware se configura en las rutas en Laravel 12+

    /**
     * Listar roles del tenant actual
     */
    public function index(Request $request)
    {
        $tenantId = session('tenant_id');
        $this->verificarAdmin();

        $roles = Role::where('tenant_id', $tenantId)
            ->with('permissions', 'users')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('admin.roles.index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Formulario para crear rol
     */
    public function create()
    {
        $this->verificarAdmin();

        $permissions = Permission::all();
        $permissionsByResource = $permissions->groupBy('resource');

        return view('admin.roles.create', [
            'permissionsByResource' => $permissionsByResource,
        ]);
    }

    /**
     * Guardar nuevo rol
     */
    public function store(Request $request)
    {
        $tenantId = session('tenant_id');
        $this->verificarAdmin();

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            $role = Role::create([
                'tenant_id' => $tenantId,
                'name' => $validated['name'],
                'slug' => Str::slug($validated['name']),
                'description' => $validated['description'],
                'is_base' => false,
            ]);

            // Asignar permisos
            $role->permissions()->attach($validated['permissions']);

            // Registrar en auditoría
            AuditService::log('create', 'role', $role->id, null, [
                'nombre' => $role->name,
                'permisos' => count($validated['permissions']),
            ]);

            return redirect()->route('admin.roles.index')
                ->with('success', "Rol '{$role->name}' creado exitosamente");

        } catch (\Exception $e) {
            return back()->with('error', 'Error creando rol: ' . $e->getMessage());
        }
    }

    /**
     * Formulario para editar rol
     */
    public function edit($id)
    {
        $tenantId = session('tenant_id');
        $this->verificarAdmin();

        $role = Role::where('tenant_id', $tenantId)->findOrFail($id);

        if ($role->is_base) {
            return back()->with('error', 'No se pueden editar roles base');
        }

        $permissions = Permission::all();
        $permissionsByResource = $permissions->groupBy('resource');
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', [
            'role' => $role,
            'permissionsByResource' => $permissionsByResource,
            'rolePermissions' => $rolePermissions,
        ]);
    }

    /**
     * Actualizar rol
     */
    public function update(Request $request, $id)
    {
        $tenantId = session('tenant_id');
        $this->verificarAdmin();

        $role = Role::where('tenant_id', $tenantId)->findOrFail($id);

        if ($role->is_base) {
            return back()->with('error', 'No se pueden editar roles base');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'required|array|min:1',
            'permissions.*' => 'exists:permissions,id',
        ]);

        try {
            $oldData = [
                'nombre' => $role->name,
                'permisos' => $role->permissions->pluck('name')->toArray(),
            ];

            $role->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
            ]);

            // Actualizar permisos
            $role->permissions()->sync($validated['permissions']);

            $newData = [
                'nombre' => $role->name,
                'permisos' => $role->permissions->pluck('name')->toArray(),
            ];

            // Registrar en auditoría
            AuditService::log('update', 'role', $role->id, $oldData, $newData);

            return redirect()->route('admin.roles.index')
                ->with('success', 'Rol actualizado');

        } catch (\Exception $e) {
            return back()->with('error', 'Error actualizando rol: ' . $e->getMessage());
        }
    }

    /**
     * Eliminar rol (solo si no es base y no tiene usuarios)
     */
    public function destroy($id)
    {
        $tenantId = session('tenant_id');
        $this->verificarAdmin();

        $role = Role::where('tenant_id', $tenantId)->findOrFail($id);

        if ($role->is_base) {
            return back()->with('error', 'No se pueden eliminar roles base');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'No se puede eliminar un rol que tiene usuarios asignados');
        }

        try {
            AuditService::log('delete', 'role', $role->id, [
                'nombre' => $role->name,
                'permisos' => $role->permissions->pluck('name')->toArray(),
            ]);

            $role->delete();

            return redirect()->route('admin.roles.index')
                ->with('success', 'Rol eliminado');

        } catch (\Exception $e) {
            return back()->with('error', 'Error eliminando rol: ' . $e->getMessage());
        }
    }

    /**
     * Cambiar rol de un usuario
     */
    public function asignarRol(Request $request, $userId)
    {
        $tenantId = session('tenant_id');
        $this->verificarAdmin();

        $validated = $request->validate([
            'role_id' => 'required|exists:roles,id',
        ]);

        $user = User::where('tenant_id', $tenantId)->findOrFail($userId);
        $newRole = Role::where('tenant_id', $tenantId)->findOrFail($validated['role_id']);

        $oldRole = $user->role?->name ?? 'Sin rol';
        $user->update(['role_id' => $validated['role_id']]);

        // Registrar en auditoría
        AuditService::log('update', 'user_role', $user->id, [
            'rol_anterior' => $oldRole,
        ], [
            'rol_nuevo' => $newRole->name,
        ]);

        return back()->with('success', "Rol del usuario actualizado a {$newRole->name}");
    }

    /**
     * Verificar que el usuario es admin
     */
    private function verificarAdmin()
    {
        $tenantId = session('tenant_id');
        $user = auth()->user();

        // El usuario debe tener un rol con permiso admin.gestionar_roles
        if (!$user->hasPermission('admin.gestionar_roles')) {
            abort(403, 'No tienes permiso para gestionar roles');
        }
    }
}

