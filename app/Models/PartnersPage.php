<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class PartnersPage extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'title',
        'subtitle',
        'value_badges',
        'home_subtitle',
        'home_title',
        'home_title_highlight',
        'home_description',
    ];

    protected $fillable = [
        'title',
        'subtitle',
        'icon',
        'value_badges',
        'is_active',
        'home_subtitle',
        'home_title',
        'home_title_highlight',
        'home_description',
    ];

    protected $casts = [
        'value_badges' => 'array',
        'is_active' => 'boolean',
    ];
}
