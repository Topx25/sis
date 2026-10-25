<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class AboutSection extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'title',
        'headline',
        'subheadline',
    ];

    protected $fillable = [
        'title',
        'headline',
        'subheadline',
        'paragraphs',
        'mission',
        'vision',
        'why_choose_us',
        'pillars',
        'principles',
        'is_active',
    ];

    protected $casts = [
        'paragraphs' => 'array',
        'mission' => 'array',
        'vision' => 'array',
        'why_choose_us' => 'array',
        'pillars' => 'array',
        'principles' => 'array',
        'is_active' => 'boolean',
    ];
}
