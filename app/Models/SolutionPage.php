<?php

namespace App\Models;

use App\Traits\HasSeoMetadata;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class SolutionPage extends Model
{
    use HasFactory, HasTranslations, HasSeoMetadata;

    public array $translatable = [
        'title',
        'subtitle',
        'description',
        'badges',
        'services',
        'approach_title',
        'approach_steps',
        'why_us_title',
        'why_us_points',
        'industries_title',
        'industries',
        'cta_headline',
        'cta_subheadline',
        'cta_button_text',
    ];

    protected $fillable = [
        'slug',
        'title',
        'subtitle',
        'description',
        'icon',
        'hero_image',
        'badges',
        'services',
        'approach_title',
        'approach_steps',
        'why_us_title',
        'why_us_points',
        'industries_title',
        'industries',
        'cta_headline',
        'cta_subheadline',
        'cta_button_text',
        'cta_button_link',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
