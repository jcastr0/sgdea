<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Permission extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'display_name',
        'description',
        'resource',
        'action',
    ];

    /**
     * Relación muchos a muchos: Un permiso pertenece a muchos roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'role_permission');
    }

    /**
     * Scope: obtener permisos por recurso
     */
    public function scopeByResource($query, $resource)
    {
        return $query->where('resource', $resource);
    }

    /**
     * Scope: obtener permisos por nombre
     */
    public function scopeByName($query, $name)
    {
        return $query->where('name', $name);
    }
}

