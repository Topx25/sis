<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AboutHero extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'tagline',
        'headline',
        'subheadline',
        'cta_text',
    ];

    protected $fillable = [
        'tagline',
        'headline',
        'subheadline',
        'cta_text',
        'cta_link',
        'image',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
