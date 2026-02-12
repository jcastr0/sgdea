<?php

namespace App\Policies;

use App\Models\Role;
use App\Models\User;

class RolePolicy
{
    /**
     * Ver listado de roles
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->isSuperAdmin();
    }

    /**
     * Ver detalles de un rol
     */
    public function view(User $authUser, Role $role): bool
    {
        return $authUser->isSuperAdmin();
    }

    /**
     * Crear rol
     */
    public function create(User $authUser): bool
    {
        return $authUser->isSuperAdmin();
    }

    /**
     * Actualizar rol
     */
    public function update(User $authUser, Role $role): bool
    {
        return $authUser->isSuperAdmin();
    }

    /**
     * Eliminar rol
     */
    public function delete(User $authUser, Role $role): bool
    {
        // No permitir eliminar roles del sistema
        if (in_array($role->name, ['Superadmin', 'Admin', 'Manager', 'User'])) {
            return false;
        }

        return $authUser->isSuperAdmin();
    }
}

