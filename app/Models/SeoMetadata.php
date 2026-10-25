<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;

class SeoMetadata extends Model
{
    use HasFactory, HasTranslations;

    protected $table = 'seo_metadata';

    public array $translatable = [
        'meta_title',
        'meta_description',
        'og_title',
        'og_description',
    ];

    protected $fillable = [
        'seoable_type',
        'seoable_id',
        'meta_title',
        'meta_description',
        'og_title',
        'og_description',
        'og_image',
        'canonical_url',
        'is_noindex',
        'is_nofollow',
        'schema_type',
        'schema_overrides',
    ];

    protected $casts = [
        'is_noindex' => 'boolean',
        'is_nofollow' => 'boolean',
        'schema_overrides' => 'array',
    ];

    /**
     * Get the owning seoable model.
     */
    public function seoable(): MorphTo
    {
        return $this->morphTo();
    }
}
