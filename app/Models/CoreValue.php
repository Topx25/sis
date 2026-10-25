<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class CoreValue extends Model
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
        'principles',
        'is_active',
    ];

    protected $casts = [
        'principles' => 'array',
        'is_active' => 'boolean',
    ];
}
