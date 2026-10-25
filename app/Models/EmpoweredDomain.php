<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class EmpoweredDomain extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'tagline',
        'headline',
        'subheadline',
    ];

    protected $fillable = [
        'tagline',
        'headline',
        'subheadline',
        'domains',
        'is_active',
    ];

    protected $casts = [
        'domains' => 'array',
        'is_active' => 'boolean',
    ];
}
