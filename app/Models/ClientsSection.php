<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClientsSection extends Model
{
    use HasFactory;

    protected $fillable = [
        'subtitle',
        'title',
        'title_highlight',
        'description',
    ];
}
