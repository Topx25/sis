<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContactRequest extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'email', 'phone', 'company_name', 'message', 'status', 'reply', 'replied_at'];

    protected $casts = [
        'replied_at' => 'datetime',
    ];
}
