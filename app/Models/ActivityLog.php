<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ActivityLog extends Model
{
    public const UPDATED_AT = null; // only created_at is tracked

    protected $fillable = [
        'user_id',
        'user_name',
        'method',
        'path',
        'ip',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'status'     => 'integer',
    ];
}
