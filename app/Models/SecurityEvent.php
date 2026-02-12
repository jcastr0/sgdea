<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SecurityEvent extends Model
{
    use HasFactory;

    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = [
        'company_id',
        'user_id',
        'event_type',
        'entity_type',
        'entity_id',
        'ip_address',
        'user_agent',
        'browser',
        'operating_system',
        'device_type',
        'country',
        'description',
        'metadata',
    ];

    protected $casts = [
        'metadata' => 'json',
        'created_at' => 'datetime',
    ];

    /**
     * Relación con usuario
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}

