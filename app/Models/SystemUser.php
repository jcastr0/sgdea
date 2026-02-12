<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class SystemUser extends Authenticatable
{
    use Notifiable;
    protected $fillable = [
        'email',
        'password',
        'name',
        'is_superadmin',
        'email_verified_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_superadmin' => 'boolean',
    ];

    /**
     * Relación: Un SystemUser puede tener muchos tenants
     */
    public function tenants()
    {
        return $this->hasMany(Tenant::class, 'superadmin_id');
    }

    /**
     * Scope: Obtener superadmins
     */
    public function scopeSuperadmins($query)
    {
        return $query->where('is_superadmin', true);
    }

    /**
     * Scope: Obtener por email
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email)->first();
    }
}

