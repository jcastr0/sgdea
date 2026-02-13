<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Tenant;
use App\Models\Role;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * ============================================================================
 * CONTROLADOR: GlobalUserController
 * ============================================================================
 *
 * Controlador para la gestión global de usuarios desde el panel Superadmin.
 * Permite ver, crear, editar y eliminar usuarios de cualquier tenant.
 *
 * @author SGDEA Team
 * ============================================================================
 */
class GlobalUserController extends Controller
{
    /**
     * Mostrar listado global de usuarios
     */
    public function index()
    {
        return view('admin.users.index');
    }

    /**
     * Mostrar detalle de un usuario
     */
    public function show(User $user)
    {
        $user->load(['tenant', 'role']);

        return view('admin.users.show', [
            'user' => $user,
        ]);
    }

    /**
     * Formulario para crear usuario
     */
    public function create()
    {
        $tenants = Tenant::where('status', 'active')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.create', [
            'tenants' => $tenants,
            'roles' => $roles,
        ]);
    }

    /**
     * Guardar nuevo usuario
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'tenant_id' => 'required|exists:tenants,id',
            'role_id' => 'required|exists:roles,id',
            'password' => 'nullable|string|min:8',
            'status' => 'required|in:active,pending_approval,blocked,inactive',
        ]);

        // Generar password si no se proporcionó
        $password = $validated['password'] ?? Str::random(12);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'tenant_id' => $validated['tenant_id'],
            'role_id' => $validated['role_id'],
            'password' => Hash::make($password),
            'status' => $validated['status'],
            'email_verified_at' => now(),
        ]);

        return redirect()->route('admin.users.index')
            ->with('success', "Usuario '{$user->name}' creado correctamente. Contraseña: {$password}");
    }

    /**
     * Formulario de edición de usuario
     */
    public function edit(User $user)
    {
        $tenants = Tenant::where('status', 'active')->orderBy('name')->get();
        $roles = Role::orderBy('name')->get();

        return view('admin.users.edit', [
            'user' => $user,
            'tenants' => $tenants,
            'roles' => $roles,
        ]);
    }

    /**
     * Actualizar usuario
     */
    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'tenant_id' => 'required|exists:tenants,id',
            'role_id' => 'required|exists:roles,id',
            'status' => 'required|in:active,pending_approval,blocked,inactive',
        ]);

        $user->update($validated);

        return redirect()->route('admin.users.index')
            ->with('success', "Usuario '{$user->name}' actualizado correctamente.");
    }

    /**
     * Cambiar estado del usuario (activar/suspender)
     */
    public function toggleStatus(User $user)
    {
        // No permitir cambiar estado de superadmins
        if ($user->role && $user->role->slug === 'super_admin') {
            return back()->with('error', 'No se puede modificar el estado de un Superadmin Global.');
        }

        $newStatus = $user->status === 'active' ? 'blocked' : 'active';
        $user->update(['status' => $newStatus]);

        $message = $newStatus === 'active'
            ? "Usuario '{$user->name}' activado correctamente."
            : "Usuario '{$user->name}' suspendido correctamente.";

        return back()->with('success', $message);
    }

    /**
     * Resetear contraseña del usuario
     */
    public function resetPassword(User $user)
    {
        $newPassword = Str::random(12);
        $user->update(['password' => Hash::make($newPassword)]);

        return back()->with('success', "Contraseña de '{$user->name}' reseteada. Nueva contraseña: {$newPassword}");
    }

    /**
     * Eliminar usuario
     */
    public function destroy(User $user)
    {
        // No permitir eliminar superadmins
        if ($user->role && $user->role->slug === 'super_admin') {
            return back()->with('error', 'No se puede eliminar un Superadmin Global.');
        }

        // No permitir eliminar usuario SYSTEM
        if ($user->id === 1) {
            return back()->with('error', 'No se puede eliminar el usuario SYSTEM.');
        }

        $userName = $user->name;
        $user->delete();

        return redirect()->route('admin.users.index')
            ->with('success', "Usuario '{$userName}' eliminado correctamente.");
    }
}

