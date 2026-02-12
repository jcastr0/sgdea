<?php

namespace App\Models;

use App\Traits\Auditable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportLog extends Model
{
    use Auditable;

    protected $table = 'import_logs';

    protected $fillable = [
        'tenant_id',
        'import_type',
        'file_name',
        'status',
        'total_records',
        'successful',
        'failed',
        'error_details',
    ];

    protected $casts = [
        'error_details' => 'json',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function records(): HasMany
    {
        return $this->hasMany(ImportRecord::class);
    }

    public function scopeByTenant($query, $tenantId)
    {
        return $query->where('tenant_id', $tenantId);
    }

    public function scopeByType($query, $type)
    {
        return $query->where('import_type', $type);
    }
}

