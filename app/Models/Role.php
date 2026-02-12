<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    protected $fillable = [
        'tenant_id',
        'name',
        'slug',
        'description',
        'is_system',
        'is_default',
        'priority',
    ];

    protected $casts = [
        'is_system' => 'boolean',
        'is_default' => 'boolean',
        'priority' => 'integer',
    ];

    /**
     * Relación: Un rol pertenece a un tenant
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relación muchos a muchos: Un rol tiene muchos permisos
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class, 'role_permission');
    }

    /**
     * Relación uno a muchos: Un rol tiene muchos usuarios
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Verificar si el rol tiene un permiso específico
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->permissions()
            ->where('name', $permissionName)
            ->exists();
    }

    /**
     * Verificar si el rol tiene múltiples permisos
     */
    public function hasPermissions(array $permissionNames): bool
    {
        foreach ($permissionNames as $permission) {
            if (!$this->hasPermission($permission)) {
                return false;
            }
        }
        return true;
    }

    /**
     * Verificar si el rol tiene al menos uno de los permisos
     */
    public function hasAnyPermission(array $permissionNames): bool
    {
        foreach ($permissionNames as $permission) {
            if ($this->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Scope: obtener roles de un tenant
     */
    public function scopeOfTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }
}

