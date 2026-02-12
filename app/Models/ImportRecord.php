<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImportRecord extends Model
{
    protected $table = 'import_records';

    protected $fillable = [
        'import_log_id',
        'factura_id',
        'tercero_id',
        'cufe',
        'numero_factura',
        'nit',
        'status',
        'error_message',
    ];

    public function importLog(): BelongsTo
    {
        return $this->belongsTo(ImportLog::class);
    }

    public function factura(): BelongsTo
    {
        return $this->belongsTo(Factura::class);
    }

    public function tercero(): BelongsTo
    {
        return $this->belongsTo(Tercero::class);
    }

    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }
}

