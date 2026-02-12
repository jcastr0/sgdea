<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportJob extends Model
{
    protected $table = 'import_jobs';

    protected $fillable = [
        'company_id',
        'user_id',
        'tipo_importacion', // 'excel' o 'pdf'
        'nombre_archivo',
        'ruta_archivo',
        'estado', // 'pendiente', 'procesando', 'completado', 'error'
        'total_registros',
        'registros_procesados',
        'registros_exitosos',
        'registros_error',
        'fecha_inicio',
        'fecha_fin',
        'mensaje_error',
        'metadata',
    ];

    protected $casts = [
        'fecha_inicio' => 'datetime',
        'fecha_fin' => 'datetime',
        'metadata' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación: Un job tiene muchos registros de error
     */
    public function errores(): HasMany
    {
        return $this->hasMany(ImportError::class, 'import_job_id');
    }

    /**
     * Obtener porcentaje de progreso
     */
    public function getPorcentajeProgreso(): float
    {
        if ($this->total_registros === 0) {
            return 0;
        }

        return round(($this->registros_procesados / $this->total_registros) * 100, 2);
    }

    /**
     * Verificar si está completado
     */
    public function estáCompletado(): bool
    {
        return $this->estado === 'completado';
    }

    /**
     * Verificar si tiene errores
     */
    public function tieneErrores(): bool
    {
        return $this->registros_error > 0;
    }
}

