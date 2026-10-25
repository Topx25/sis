<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class HeroSection extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'tagline',
        'headline',
        'subheadline',
        'cta_primary',
        'cta_secondary',
        'badge_text',
    ];

    protected $fillable = [
        'tagline',
        'headline',
        'subheadline',
        'image',
        'cta_primary',
        'cta_primary_link',
        'cta_secondary',
        'cta_secondary_link',
        'stats',
        'badge_text',
        'is_active',
    ];

    protected $casts = [
        'stats' => 'array',
        'is_active' => 'boolean',
    ];
}
