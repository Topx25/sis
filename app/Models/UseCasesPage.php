<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class UseCasesPage extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'title',
        'subtitle',
        'cta_text',
        'stats',
        'pillars',
    ];

    protected $fillable = [
        'title',
        'subtitle',
        'cta_text',
        'cta_link',
        'stats',
        'pillars',
        'is_active',
    ];

    protected $casts = [
        'stats' => 'array',
        'pillars' => 'array',
        'is_active' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function ($page) {
            \App\Support\CacheManager::invalidateUseCases();
        });

        static::deleted(function ($page) {
            \App\Support\CacheManager::invalidateUseCases();
        });
    }
}
