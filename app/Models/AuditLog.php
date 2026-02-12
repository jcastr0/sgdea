<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ============================================================================
 * MODELO: AuditLog
 * ============================================================================
 *
 * Registro de auditoría unificado del sistema SGDEA.
 * Registra todas las acciones para trazabilidad y cumplimiento normativo.
 *
 * CARACTERÍSTICAS ESPECIALES:
 * - MODELO DE SOLO LECTURA: No permite update ni delete
 * - Solo tiene created_at (registros inmutables)
 * - El usuario SYSTEM (id=1) se usa para acciones sin usuario autenticado
 * - Incluye hash SHA256 para verificar integridad del registro
 *
 * @property int $id
 * @property int $tenant_id
 * @property int $user_id
 * @property string $action
 * @property string|null $model_type
 * @property int|null $model_id
 * @property array|null $old_values
 * @property array|null $new_values
 * @property string|null $ip_address
 * @property string|null $user_agent
 * @property string|null $url
 * @property string|null $method
 * @property array|null $context
 * @property string|null $hash
 * @property \Carbon\Carbon $created_at
 *
 * @property-read User $user
 * @property-read Tenant $tenant
 */
class AuditLog extends Model
{
    // ===========================
    // CONFIGURACIÓN DE TABLA
    // ===========================

    protected $table = 'audit_logs';

    /**
     * Desactivar timestamps automáticos (solo usamos created_at manual)
     */
    public $timestamps = false;

    // ===========================
    // CONSTANTES DE ACCIONES
    // ===========================

    /** Inicio de sesión exitoso */
    public const ACTION_LOGIN = 'login';

    /** Intento de login fallido */
    public const ACTION_LOGIN_FAILED = 'login_failed';

    /** Cierre de sesión */
    public const ACTION_LOGOUT = 'logout';

    /** Creación de registro */
    public const ACTION_CREATE = 'create';

    /** Actualización de registro */
    public const ACTION_UPDATE = 'update';

    /** Eliminación de registro */
    public const ACTION_DELETE = 'delete';

    /** Restauración de registro (soft delete) */
    public const ACTION_RESTORE = 'restore';

    /** Visualización de registro sensible */
    public const ACTION_VIEW = 'view';

    /** Exportación de datos */
    public const ACTION_EXPORT = 'export';

    /** Importación de datos */
    public const ACTION_IMPORT = 'import';

    /** Ejecución de acción especial (aprobar, rechazar, etc.) */
    public const ACTION_EXECUTE = 'execute';

    /** Cambio de contraseña */
    public const ACTION_PASSWORD_CHANGE = 'password_change';

    /** Aprobación de usuario/documento */
    public const ACTION_APPROVE = 'approve';

    /** Rechazo de usuario/documento */
    public const ACTION_REJECT = 'reject';

    /**
     * Lista de todas las acciones válidas
     */
    public const ACTIONS = [
        self::ACTION_LOGIN,
        self::ACTION_LOGIN_FAILED,
        self::ACTION_LOGOUT,
        self::ACTION_CREATE,
        self::ACTION_UPDATE,
        self::ACTION_DELETE,
        self::ACTION_RESTORE,
        self::ACTION_VIEW,
        self::ACTION_EXPORT,
        self::ACTION_IMPORT,
        self::ACTION_EXECUTE,
        self::ACTION_PASSWORD_CHANGE,
        self::ACTION_APPROVE,
        self::ACTION_REJECT,
    ];

    /**
     * Descripciones legibles de las acciones
     */
    public const ACTION_DESCRIPTIONS = [
        self::ACTION_LOGIN => 'Inicio de sesión',
        self::ACTION_LOGIN_FAILED => 'Intento de login fallido',
        self::ACTION_LOGOUT => 'Cierre de sesión',
        self::ACTION_CREATE => 'Creación',
        self::ACTION_UPDATE => 'Modificación',
        self::ACTION_DELETE => 'Eliminación',
        self::ACTION_RESTORE => 'Restauración',
        self::ACTION_VIEW => 'Consulta',
        self::ACTION_EXPORT => 'Exportación',
        self::ACTION_IMPORT => 'Importación',
        self::ACTION_EXECUTE => 'Ejecución',
        self::ACTION_PASSWORD_CHANGE => 'Cambio de contraseña',
        self::ACTION_APPROVE => 'Aprobación',
        self::ACTION_REJECT => 'Rechazo',
    ];

    // ===========================
    // CONFIGURACIÓN DEL MODELO
    // ===========================

    /**
     * Campos que se pueden asignar masivamente
     */
    protected $fillable = [
        'tenant_id',
        'user_id',
        'action',
        'model_type',
        'model_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'url',
        'method',
        'context',
        'hash',
        'created_at',
    ];

    /**
     * Casting de atributos
     */
    protected $casts = [
        'tenant_id' => 'integer',
        'user_id' => 'integer',
        'model_id' => 'integer',
        'old_values' => 'array',
        'new_values' => 'array',
        'context' => 'array',
        'created_at' => 'datetime',
    ];

    // ===========================
    // RELACIONES
    // ===========================

    /**
     * Usuario que ejecutó la acción
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Tenant donde ocurrió la acción
     */
    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class, 'tenant_id');
    }

    // ===========================
    // SCOPES
    // ===========================

    /**
     * Filtrar por acción
     */
    public function scopePorAccion(Builder $query, string $action): Builder
    {
        return $query->where('action', $action);
    }

    /**
     * Filtrar por múltiples acciones
     */
    public function scopePorAcciones(Builder $query, array $actions): Builder
    {
        return $query->whereIn('action', $actions);
    }

    /**
     * Filtrar por tipo de modelo
     */
    public function scopePorModelo(Builder $query, string $modelType): Builder
    {
        return $query->where('model_type', $modelType);
    }

    /**
     * Filtrar por modelo específico (tipo + id)
     */
    public function scopePorRegistro(Builder $query, string $modelType, int $modelId): Builder
    {
        return $query->where('model_type', $modelType)
                     ->where('model_id', $modelId);
    }

    /**
     * Filtrar por usuario
     */
    public function scopePorUsuario(Builder $query, int $userId): Builder
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Filtrar por tenant
     */
    public function scopePorTenant(Builder $query, int $tenantId): Builder
    {
        return $query->where('tenant_id', $tenantId);
    }

    /**
     * Filtrar por rango de fechas
     */
    public function scopeEntreFechas(Builder $query, $desde, $hasta): Builder
    {
        return $query->whereBetween('created_at', [$desde, $hasta]);
    }

    /**
     * Filtrar por fecha específica
     */
    public function scopeEnFecha(Builder $query, $fecha): Builder
    {
        return $query->whereDate('created_at', $fecha);
    }

    /**
     * Filtrar por IP
     */
    public function scopePorIp(Builder $query, string $ip): Builder
    {
        return $query->where('ip_address', $ip);
    }

    /**
     * Solo acciones de login (exitoso y fallido)
     */
    public function scopeLogins(Builder $query): Builder
    {
        return $query->whereIn('action', [self::ACTION_LOGIN, self::ACTION_LOGIN_FAILED]);
    }

    /**
     * Solo acciones CRUD
     */
    public function scopeCrud(Builder $query): Builder
    {
        return $query->whereIn('action', [
            self::ACTION_CREATE,
            self::ACTION_UPDATE,
            self::ACTION_DELETE,
            self::ACTION_RESTORE,
        ]);
    }

    /**
     * Ordenar por más reciente
     */
    public function scopeRecientes(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    // ===========================
    // PROTECCIÓN - SOLO LECTURA
    // ===========================

    /**
     * Boot del modelo - hacer el modelo de solo lectura
     */
    protected static function boot(): void
    {
        parent::boot();

        // Bloquear actualizaciones
        static::updating(function () {
            throw new \RuntimeException('Los registros de auditoría son inmutables y no pueden ser modificados.');
        });

        // Bloquear eliminaciones
        static::deleting(function () {
            throw new \RuntimeException('Los registros de auditoría son inmutables y no pueden ser eliminados.');
        });

        // Asegurar created_at y generar hash al crear
        static::creating(function (AuditLog $log) {
            if (empty($log->created_at)) {
                $log->created_at = now();
            }

            // Generar hash de integridad
            $log->hash = $log->generateHash();
        });
    }

    /**
     * Generar hash SHA256 para verificar integridad
     */
    public function generateHash(): string
    {
        $data = [
            'tenant_id' => $this->tenant_id,
            'user_id' => $this->user_id,
            'action' => $this->action,
            'model_type' => $this->model_type,
            'model_id' => $this->model_id,
            'old_values' => $this->old_values,
            'new_values' => $this->new_values,
            'ip_address' => $this->ip_address,
            'created_at' => $this->created_at?->toIso8601String(),
        ];

        return hash('sha256', json_encode($data));
    }

    /**
     * Verificar integridad del registro
     */
    public function verificarIntegridad(): bool
    {
        return $this->hash === $this->generateHash();
    }

    // ===========================
    // HELPERS DE REGISTRO
    // ===========================

    /**
     * Crear un nuevo registro de auditoría
     */
    public static function registrar(
        string $action,
        int $tenantId,
        ?int $userId = null,
        ?string $modelType = null,
        ?int $modelId = null,
        ?array $oldValues = null,
        ?array $newValues = null,
        ?array $context = null
    ): self {
        // Usar SYSTEM si no hay usuario
        $userId = $userId ?? User::SYSTEM_ID;

        // Capturar información del request
        $request = request();

        return self::create([
            'tenant_id' => $tenantId,
            'user_id' => $userId,
            'action' => $action,
            'model_type' => $modelType,
            'model_id' => $modelId,
            'old_values' => $oldValues,
            'new_values' => $newValues,
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'url' => $request?->fullUrl(),
            'method' => $request?->method(),
            'context' => $context,
            'created_at' => now(),
        ]);
    }

    /**
     * Registrar login exitoso
     */
    public static function registrarLogin(int $tenantId, int $userId, ?array $context = null): self
    {
        return self::registrar(
            self::ACTION_LOGIN,
            $tenantId,
            $userId,
            User::class,
            $userId,
            null,
            null,
            $context
        );
    }

    /**
     * Registrar login fallido
     */
    public static function registrarLoginFallido(int $tenantId, string $username, ?string $razon = null): self
    {
        return self::registrar(
            self::ACTION_LOGIN_FAILED,
            $tenantId,
            User::SYSTEM_ID,
            null,
            null,
            null,
            null,
            [
                'username_intentado' => $username,
                'razon' => $razon ?? 'invalid_credentials',
            ]
        );
    }

    /**
     * Registrar logout
     */
    public static function registrarLogout(int $tenantId, int $userId): self
    {
        return self::registrar(
            self::ACTION_LOGOUT,
            $tenantId,
            $userId,
            User::class,
            $userId
        );
    }

    // ===========================
    // HELPERS DE VISUALIZACIÓN
    // ===========================

    /**
     * Obtener descripción legible de la acción
     */
    public function getDescripcionAccion(): string
    {
        return self::ACTION_DESCRIPTIONS[$this->action] ?? $this->action;
    }

    /**
     * Obtener nombre corto del modelo afectado
     */
    public function getNombreModelo(): ?string
    {
        if (!$this->model_type) {
            return null;
        }

        return class_basename($this->model_type);
    }

    /**
     * Obtener el nombre de la tabla (método estático para uso externo)
     */
    public static function getTableName(): string
    {
        return (new self())->getTable();
    }

    /**
     * Obtener color de badge según la acción
     */
    public function getColorAccion(): string
    {
        return match ($this->action) {
            self::ACTION_LOGIN => 'green',
            self::ACTION_LOGIN_FAILED => 'red',
            self::ACTION_LOGOUT => 'gray',
            self::ACTION_CREATE => 'blue',
            self::ACTION_UPDATE => 'yellow',
            self::ACTION_DELETE => 'red',
            self::ACTION_RESTORE => 'green',
            self::ACTION_VIEW => 'gray',
            self::ACTION_EXPORT => 'purple',
            self::ACTION_IMPORT => 'indigo',
            self::ACTION_EXECUTE => 'orange',
            self::ACTION_APPROVE => 'green',
            self::ACTION_REJECT => 'red',
            default => 'gray',
        };
    }

    /**
     * Obtener icono según la acción
     */
    public function getIconoAccion(): string
    {
        return match ($this->action) {
            self::ACTION_LOGIN => 'login',
            self::ACTION_LOGIN_FAILED => 'x-circle',
            self::ACTION_LOGOUT => 'logout',
            self::ACTION_CREATE => 'plus-circle',
            self::ACTION_UPDATE => 'pencil',
            self::ACTION_DELETE => 'trash',
            self::ACTION_RESTORE => 'refresh',
            self::ACTION_VIEW => 'eye',
            self::ACTION_EXPORT => 'download',
            self::ACTION_IMPORT => 'upload',
            self::ACTION_EXECUTE => 'play',
            self::ACTION_APPROVE => 'check-circle',
            self::ACTION_REJECT => 'x-circle',
            default => 'document',
        };
    }
}

