<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportError extends Model
{
    protected $table = 'import_errors';

    protected $fillable = [
        'import_job_id',
        'numero_fila',
        'cufe',
        'numero_factura',
        'tipo_error', // 'validacion', 'duplicado', 'integridad', 'extraccion'
        'mensaje_error',
        'datos_fila',
    ];

    protected $casts = [
        'datos_fila' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación: Un error pertenece a un job
     */
    public function job(): BelongsTo
    {
        return $this->belongsTo(ImportJob::class, 'import_job_id');
    }
}

