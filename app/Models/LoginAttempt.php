<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoginAttempt extends Model
{
    use HasFactory;

    public $timestamps = true;
    const UPDATED_AT = null;

    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'success',
        'reason',
    ];

    protected $casts = [
        'success' => 'boolean',
        'created_at' => 'datetime',
    ];
}

