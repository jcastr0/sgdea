<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SetupCheckpoint extends Model
{
    protected $fillable = [
        'step_key',
        'step_name',
        'step_order',
        'status',
        'phase',
        'component',
        'completion_date',
        'error_message',
    ];

    protected $casts = [
        'completion_date' => 'datetime',
    ];

    /**
     * Scope: Obtener pasos pendientes
     */
    public function scopePending($query)
    {
        return $query->where('status', 'pending')->orderBy('step_order');
    }

    /**
     * Scope: Obtener pasos completados
     */
    public function scopeCompleted($query)
    {
        return $query->where('status', 'completed')->orderBy('step_order');
    }

    /**
     * Scope: Obtener pasos por fase
     */
    public function scopeByPhase($query, $phase)
    {
        return $query->where('phase', $phase)->orderBy('step_order');
    }

    /**
     * Scope: Obtener pasos por estado
     */
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status)->orderBy('step_order');
    }

    /**
     * Obtener siguiente paso pendiente
     */
    public static function getNextPending()
    {
        return self::pending()->first();
    }

    /**
     * Contar pasos completados
     */
    public static function countCompleted()
    {
        return self::completed()->count();
    }

    /**
     * Contar total de pasos
     */
    public static function countTotal()
    {
        return self::count();
    }
}

