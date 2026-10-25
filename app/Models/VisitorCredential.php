<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class VisitorCredential extends Model
{
    use HasFactory;

    protected $fillable = [
        'username',
        'password',
        'is_active',
        'expires_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'expires_at' => 'datetime',
    ];

    protected $appends = [
        'days_until_expiry',
    ];

    public function getDaysUntilExpiryAttribute(): int
    {
        if (!$this->expires_at) {
            return 0;
        }

        $now = Carbon::now();
        if ($now->greaterThanOrEqualTo($this->expires_at)) {
            return 0;
        }

        return (int) ceil($now->diffInHours($this->expires_at, false) / 24);
    }
}
