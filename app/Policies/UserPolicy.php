<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Ver listado de usuarios
     */
    public function viewAny(User $authUser): bool
    {
        return $authUser->isSuperAdmin();
    }

    /**
     * Ver detalles de un usuario
     */
    public function view(User $authUser, User $user): bool
    {
        return $authUser->isSuperAdmin() || $authUser->id === $user->id;
    }

    /**
     * Crear usuario
     */
    public function create(User $authUser): bool
    {
        return $authUser->isSuperAdmin();
    }

    /**
     * Actualizar usuario
     */
    public function update(User $authUser, User $user): bool
    {
        return $authUser->isSuperAdmin() || $authUser->id === $user->id;
    }

    /**
     * Eliminar usuario
     */
    public function delete(User $authUser, User $user): bool
    {
        return $authUser->isSuperAdmin() && $user->id !== $authUser->id;
    }

    /**
     * Ver usuarios pendientes de aprobación
     */
    public function viewPending(User $authUser): bool
    {
        return $authUser->isSuperAdmin();
    }

    /**
     * Aprobar usuario
     */
    public function approve(User $authUser, User $user): bool
    {
        return $authUser->isSuperAdmin() && $user->id !== $authUser->id;
    }

    /**
     * Bloquear usuario
     */
    public function block(User $authUser, User $user): bool
    {
        return $authUser->isSuperAdmin() && $user->id !== $authUser->id;
    }

    /**
     * Asignar rol a usuario
     */
    public function assignRole(User $authUser, User $user): bool
    {
        return $authUser->isSuperAdmin() && $user->id !== $authUser->id;
    }
}

