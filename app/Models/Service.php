<?php

namespace App\Models;

use App\Traits\HasSeoMetadata;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;

class Service extends Model
{
    use HasFactory, HasTranslations, HasSeoMetadata;

    public array $translatable = [
        'title',
        'headline',
        'summary',
        'description',
    ];

    protected $fillable = [
        'title',
        'headline',
        'slug',
        'summary',
        'description',
        'icon',
    ];

    public function projects(): BelongsToMany
    {
        return $this->belongsToMany(Project::class);
    }

    protected static function booted()
    {
        static::saved(function ($service) {
            \App\Support\CacheManager::invalidateUseCases();
        });

        static::deleted(function ($service) {
            \App\Support\CacheManager::invalidateUseCases();
        });
    }
}
