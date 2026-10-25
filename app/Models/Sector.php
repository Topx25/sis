<?php

namespace App\Models;

use App\Traits\HasSeoMetadata;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;

class Sector extends Model
{
    use HasFactory, HasTranslations, HasSeoMetadata;

    public array $translatable = [
        'name',
        'description',
    ];

    protected $fillable = [
        'name',
        'slug',
        'icon',
        'image',
        'description',
        'sort_order',
        'is_active',
        'relevant_programs',
        'typical_scope',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'relevant_programs' => 'array',
        'typical_scope' => 'array',
    ];

    public function projects(): HasMany
    {
        return $this->hasMany(Project::class);
    }

    protected static function booted()
    {
        static::saved(function ($sector) {
            \App\Support\CacheManager::invalidateUseCases();
        });

        static::deleted(function ($sector) {
            \App\Support\CacheManager::invalidateUseCases();
        });
    }
}
