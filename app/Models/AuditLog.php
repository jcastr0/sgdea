<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditLog extends Model
{
    protected $table = 'audit_logs';

    public $timestamps = false; // Solo created_at, sin updated_at

    protected $fillable = [
        'user_id',
        'action',
        'entity_type',
        'entity_id',
        'old_values',
        'new_values',
        'ip_address',
        'user_agent',
        'created_at',
    ];

    protected $casts = [
        'old_values' => 'array',
        'new_values' => 'array',
        'created_at' => 'datetime',
    ];

    /**
     * Relación: Un audit log pertenece a un usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Scope: Filtrar por tipo de entidad
     */
    public function scopePorEntidad($query, $tipoEntidad)
    {
        return $query->where('entity_type', $tipoEntidad);
    }

    /**
     * Scope: Filtrar por acción
     */
    public function scopePorAccion($query, $accion)
    {
        return $query->where('action', $accion);
    }

    /**
     * Scope: Filtrar por usuario
     */
    public function scopePorUsuario($query, $usuarioId)
    {
        return $query->where('user_id', $usuarioId);
    }
}
