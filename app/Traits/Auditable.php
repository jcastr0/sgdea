<?php

namespace App\Traits;

use App\Models\AuditLog;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

/**
 * ============================================================================
 * TRAIT: Auditable
 * ============================================================================
 *
 * Trait para agregar auditoría automática a modelos Eloquent.
 * Registra automáticamente las acciones: created, updated, deleted, restored.
 *
 * USO:
 *   use App\Traits\Auditable;
 *   class MiModelo extends Model {
 *       use Auditable;
 *   }
 *
 * CONFIGURACIÓN (opcional en el modelo):
 *   protected $auditExclude = ['campo_a_excluir', 'otro_campo'];
 *   protected $auditIncludeOnly = ['solo', 'estos', 'campos'];
 *   protected $auditDisabled = true; // Desactivar temporalmente
 *
 * CAMPOS EXCLUIDOS POR DEFECTO:
 *   - password, remember_token, api_token
 *   - updated_at (no relevante para auditoría)
 *
 * @author SGDEA Team
 * ============================================================================
 */
trait Auditable
{
    /**
     * Campos a excluir de la auditoría por defecto (datos sensibles)
     */
    protected static array $defaultExcludedFields = [
        'password',
        'remember_token',
        'api_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
        'updated_at',
    ];

    /**
     * Boot del trait - registrar observers para auditoría automática
     */
    public static function bootAuditable(): void
    {
        // Evento: Modelo creado
        static::created(function ($model) {
            if ($model->shouldAudit()) {
                $model->auditCreated();
            }
        });

        // Evento: Modelo actualizado
        static::updated(function ($model) {
            if ($model->shouldAudit()) {
                $model->auditUpdated();
            }
        });

        // Evento: Modelo eliminado
        static::deleted(function ($model) {
            if ($model->shouldAudit()) {
                $model->auditDeleted();
            }
        });

        // Evento: Modelo restaurado (si usa SoftDeletes)
        if (method_exists(static::class, 'restored')) {
            static::restored(function ($model) {
                if ($model->shouldAudit()) {
                    $model->auditRestored();
                }
            });
        }
    }

    /**
     * Verificar si el modelo debe ser auditado
     */
    protected function shouldAudit(): bool
    {
        // Verificar si está desactivada la auditoría
        if (property_exists($this, 'auditDisabled') && $this->auditDisabled === true) {
            return false;
        }

        // No auditar el propio AuditLog (evitar recursión)
        if ($this instanceof AuditLog) {
            return false;
        }

        return true;
    }

    /**
     * Obtener el ID del tenant actual
     *
     * Devuelve null para acciones globales, usuarios sin tenant asignado,
     * o acciones sin autenticación (ej: login fallido, acciones del sistema)
     */
    protected function getAuditTenantId(): ?int
    {
        // Si el modelo tiene tenant_id, usarlo
        if (isset($this->tenant_id) && $this->tenant_id !== null) {
            return $this->tenant_id;
        }

        // Intentar obtener del usuario autenticado
        $user = Auth::user();
        if ($user && isset($user->tenant_id) && $user->tenant_id !== null) {
            return $user->tenant_id;
        }

        // Intentar obtener de sesión o request
        if (session()->has('tenant_id') && session('tenant_id') !== null) {
            return session('tenant_id');
        }

        // Sin tenant identificado, devolver null
        // Esto aplica para: superadmin global, login fallido, acciones del sistema
        return null;
    }

    /**
     * Obtener el ID del usuario actual (o SYSTEM si no hay autenticación)
     */
    protected function getAuditUserId(): int
    {
        // Intentar obtener usuario autenticado
        $user = Auth::user();

        if ($user && $user instanceof User) {
            return $user->id;
        }

        // Sin usuario autenticado, usar SYSTEM
        return User::SYSTEM_ID;
    }

    /**
     * Obtener campos a excluir de la auditoría
     */
    protected function getAuditExcludedFields(): array
    {
        $excluded = self::$defaultExcludedFields;

        // Agregar campos adicionales definidos en el modelo
        if (property_exists($this, 'auditExclude')) {
            $excluded = array_merge($excluded, $this->auditExclude);
        }

        return $excluded;
    }

    /**
     * Filtrar atributos para excluir campos sensibles
     */
    protected function filterAuditAttributes(array $attributes): array
    {
        $excluded = $this->getAuditExcludedFields();

        // Si hay lista de inclusión, usar solo esos
        if (property_exists($this, 'auditIncludeOnly') && !empty($this->auditIncludeOnly)) {
            return array_intersect_key($attributes, array_flip($this->auditIncludeOnly));
        }

        // Excluir campos sensibles
        return array_diff_key($attributes, array_flip($excluded));
    }

    /**
     * Obtener contexto adicional del request
     */
    protected function getAuditContext(): ?array
    {
        $context = [];

        // Identificar origen de la acción
        if (app()->runningInConsole()) {
            $context['source'] = 'console';
        } elseif (request()->ajax() || request()->wantsJson()) {
            $context['source'] = 'api';
        } else {
            $context['source'] = 'web';
        }

        // Agregar contexto personalizado del modelo si existe
        if (method_exists($this, 'getCustomAuditContext')) {
            $context = array_merge($context, $this->getCustomAuditContext());
        }

        return !empty($context) ? $context : null;
    }

    /**
     * Registrar auditoría de creación
     */
    protected function auditCreated(): void
    {
        $newValues = $this->filterAuditAttributes($this->getAttributes());

        AuditLog::registrar(
            AuditLog::ACTION_CREATE,
            $this->getAuditTenantId(),
            $this->getAuditUserId(),
            get_class($this),
            $this->getKey(),
            null,
            $newValues,
            $this->getAuditContext()
        );
    }

    /**
     * Registrar auditoría de actualización
     */
    protected function auditUpdated(): void
    {
        // Obtener solo los cambios
        $changes = $this->getChanges();
        $original = $this->getOriginal();

        // Filtrar campos sensibles
        $changes = $this->filterAuditAttributes($changes);

        // Si no hay cambios relevantes, no auditar
        if (empty($changes)) {
            return;
        }

        // Obtener valores anteriores solo de los campos que cambiaron
        $oldValues = [];
        foreach (array_keys($changes) as $key) {
            if (array_key_exists($key, $original)) {
                $oldValues[$key] = $original[$key];
            }
        }

        // Filtrar valores anteriores también
        $oldValues = $this->filterAuditAttributes($oldValues);

        AuditLog::registrar(
            AuditLog::ACTION_UPDATE,
            $this->getAuditTenantId(),
            $this->getAuditUserId(),
            get_class($this),
            $this->getKey(),
            $oldValues,
            $changes,
            $this->getAuditContext()
        );
    }

    /**
     * Registrar auditoría de eliminación
     */
    protected function auditDeleted(): void
    {
        $oldValues = $this->filterAuditAttributes($this->getOriginal());

        // Determinar si es soft delete o hard delete
        $isSoftDelete = in_array('Illuminate\Database\Eloquent\SoftDeletes', class_uses_recursive($this));

        $context = $this->getAuditContext() ?? [];
        if ($isSoftDelete && method_exists($this, 'trashed') && $this->trashed()) {
            $context['delete_type'] = 'soft';
        } else {
            $context['delete_type'] = 'hard';
        }

        AuditLog::registrar(
            AuditLog::ACTION_DELETE,
            $this->getAuditTenantId(),
            $this->getAuditUserId(),
            get_class($this),
            $this->getKey(),
            $oldValues,
            null,
            $context
        );
    }

    /**
     * Registrar auditoría de restauración
     */
    protected function auditRestored(): void
    {
        $newValues = $this->filterAuditAttributes($this->getAttributes());

        AuditLog::registrar(
            AuditLog::ACTION_RESTORE,
            $this->getAuditTenantId(),
            $this->getAuditUserId(),
            get_class($this),
            $this->getKey(),
            null,
            $newValues,
            $this->getAuditContext()
        );
    }

    /**
     * Registrar manualmente una acción de visualización
     * Llamar explícitamente cuando se quiera auditar consultas sensibles
     */
    public function auditView(?array $context = null): void
    {
        AuditLog::registrar(
            AuditLog::ACTION_VIEW,
            $this->getAuditTenantId(),
            $this->getAuditUserId(),
            get_class($this),
            $this->getKey(),
            null,
            null,
            $context ?? $this->getAuditContext()
        );
    }

    /**
     * Registrar manualmente una acción de ejecución
     * Para acciones especiales como "aprobar", "rechazar", "fusionar", etc.
     */
    public function auditExecute(string $descripcion, ?array $datosExtra = null): void
    {
        $context = $this->getAuditContext() ?? [];
        $context['accion'] = $descripcion;

        if ($datosExtra) {
            $context = array_merge($context, $datosExtra);
        }

        AuditLog::registrar(
            AuditLog::ACTION_EXECUTE,
            $this->getAuditTenantId(),
            $this->getAuditUserId(),
            get_class($this),
            $this->getKey(),
            null,
            null,
            $context
        );
    }

    /**
     * Registrar manualmente una acción de aprobación
     */
    public function auditApprove(?array $context = null): void
    {
        AuditLog::registrar(
            AuditLog::ACTION_APPROVE,
            $this->getAuditTenantId(),
            $this->getAuditUserId(),
            get_class($this),
            $this->getKey(),
            null,
            $this->filterAuditAttributes($this->getAttributes()),
            $context ?? $this->getAuditContext()
        );
    }

    /**
     * Registrar manualmente una acción de rechazo
     */
    public function auditReject(?string $motivo = null, ?array $context = null): void
    {
        $ctx = $context ?? $this->getAuditContext() ?? [];
        if ($motivo) {
            $ctx['motivo_rechazo'] = $motivo;
        }

        AuditLog::registrar(
            AuditLog::ACTION_REJECT,
            $this->getAuditTenantId(),
            $this->getAuditUserId(),
            get_class($this),
            $this->getKey(),
            null,
            null,
            $ctx
        );
    }

    /**
     * Desactivar temporalmente la auditoría para este modelo
     */
    public function withoutAudit(): self
    {
        $this->auditDisabled = true;
        return $this;
    }

    /**
     * Reactivar la auditoría
     */
    public function withAudit(): self
    {
        $this->auditDisabled = false;
        return $this;
    }

    /**
     * Ejecutar una operación sin auditoría
     */
    public static function withoutAuditing(callable $callback)
    {
        $model = new static();
        $model->auditDisabled = true;

        return $callback($model);
    }
}

