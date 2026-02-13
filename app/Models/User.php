<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, Auditable;

    // ===========================
    // CONSTANTES
    // ===========================

    /**
     * ID del usuario SYSTEM (para acciones sin autenticación)
     * Este usuario no puede iniciar sesión, solo se usa para auditoría
     */
    public const SYSTEM_ID = 1;

    /**
     * Email del usuario SYSTEM
     */
    public const SYSTEM_EMAIL = 'system@sgdea.local';

    // ===========================
    // CAMPOS A EXCLUIR DE AUDITORÍA
    // ===========================

    /**
     * Campos que no deben registrarse en auditoría (datos sensibles)
     */
    protected array $auditExclude = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'phone',
        'department',
        'preferences',
        'status',
        'blocked_until',
        'last_login_at',
        'last_login_ip',
        'approved_by',
        'approved_at',
        'company_id',
        'estado',
        'tenant_id',
        'role_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'blocked_until' => 'datetime',
            'last_login_at' => 'datetime',
            'approved_at' => 'datetime',
        ];
    }

    /**
     * Relación: Un usuario pertenece a un tenant (empresa)
     */
    public function tenant()
    {
        return $this->belongsTo(Tenant::class);
    }

    /**
     * Relación: Un usuario pertenece a una empresa
     */
    public function company()
    {
        return $this->belongsTo(Company::class, 'company_id');
    }

    /**
     * Relación muchos a muchos con roles
     */
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role');
    }

    /**
     * Relación muchos a muchos con grupos
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(Group::class, 'user_group');
    }

    /**
     * Relación uno a muchos con eventos de seguridad
     */
    public function securityEvents(): HasMany
    {
        return $this->hasMany(SecurityEvent::class);
    }

    /**
     * Relación: Un usuario pertenece a un rol
     */
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    /**
     * Verifica si el usuario tiene un permiso específico
     */
    public function hasPermission(string $permissionName): bool
    {
        if (!$this->role) {
            return false;
        }
        return $this->role->hasPermission($permissionName);
    }

    /**
     * Verifica si el usuario tiene múltiples permisos
     */
    public function hasPermissions(array $permissionNames): bool
    {
        if (!$this->role) {
            return false;
        }
        return $this->role->hasPermissions($permissionNames);
    }

    /**
     * Verifica si el usuario tiene al menos uno de los permisos
     */
    public function hasAnyPermission(array $permissionNames): bool
    {
        if (!$this->role) {
            return false;
        }
        return $this->role->hasAnyPermission($permissionNames);
    }

    /**
     * Verifica si el usuario está activo
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && ($this->blocked_until === null || $this->blocked_until->isPast());
    }

    /**
     * Verifica si el usuario está bloqueado
     */
    public function isBlocked(): bool
    {
        return $this->blocked_until !== null && $this->blocked_until->isFuture();
    }

    /**
     * Verifica si el usuario está pendiente de aprobación
     */
    public function isPendingApproval(): bool
    {
        return $this->status === 'pending_approval';
    }

    /**
     * Verifica si el usuario es superadmin
     */
    public function isSuperAdmin(): bool
    {
        return $this->role && $this->role->slug === 'superadmin';
    }

    /**
     * Verifica si el usuario es admin del tenant actual
     */
    public function isAdminTenant(): bool
    {
        if (!$this->role) {
            return false;
        }
        return in_array($this->role->slug, ['superadmin', 'admin']);
    }

    /**
     * Verifica si el usuario es superadmin global
     *
     * Un superadmin global es un usuario con:
     * - tenant_id = NULL (no pertenece a ningún tenant)
     * - role.slug = 'superadmin_global'
     */
    public function isSuperadminGlobal(): bool
    {
        // Debe no tener tenant y tener rol superadmin_global
        return $this->tenant_id === null
            && $this->role
            && $this->role->slug === 'superadmin_global';
    }

    /**
     * Obtiene todos los permisos del usuario
     */
    public function getAllPermissions(): array
    {
        if (!$this->role) {
            return [];
        }
        return $this->role->permissions()->pluck('name')->toArray();
    }

    /**
     * Desbloquea el usuario si el tiempo de bloqueo ha expirado
     */
    public function unlockIfExpired(): void
    {
        if ($this->isBlocked() && $this->blocked_until->isPast()) {
            $this->update(['blocked_until' => null]);
        }
    }

    /**
     * Scope: Obtener usuarios activos
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope: Obtener usuarios pendientes de aprobación
     */
    public function scopePendingApproval($query)
    {
        return $query->where('status', 'pending_approval');
    }

    /**
     * Scope: Obtener usuarios de un tenant específico
     */
    public function scopeOfTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Scope: Obtener usuario por email en un tenant
     */
    public function scopeByEmailInTenant($query, $email, $tenantId)
    {
        return $query->where('email', $email)->where('tenant_id', $tenantId);
    }

    /**
     * Scope: Obtener usuario por email (cualquier tenant)
     */
    public function scopeByEmail($query, $email)
    {
        return $query->where('email', $email)->first();
    }
}
