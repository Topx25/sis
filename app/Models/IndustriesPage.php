<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class IndustriesPage extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'tagline',
        'headline',
        'description',
        'button_text',
        'stats',
    ];

    protected $fillable = [
        'tagline',
        'headline',
        'description',
        'button_text',
        'button_link',
        'image',
        'stats',
        'is_active',
    ];

    protected $casts = [
        'stats' => 'array',
        'is_active' => 'boolean',
    ];
}
