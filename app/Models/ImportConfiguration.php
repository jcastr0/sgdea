<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ImportConfiguration extends Model
{
    protected $table = 'import_configurations';

    protected $fillable = [
        'tenant_id',
        'excel_column_mapping',
        'pdf_naming_pattern',
    ];

    protected $casts = [
        'excel_column_mapping' => 'json',
    ];

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }
}

