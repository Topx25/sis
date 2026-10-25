<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class WorkSample extends Model
{
    use HasTranslations;

    public array $translatable = [
        'title',
        'industry',
        'blurb',
        'body',
        'badge',
    ];

    protected $fillable = [
        'slug',
        'title',
        'industry',
        'category',
        'blurb',
        'body',
        'free',
        'badge',
        'demo_url',
        'image',
        'image_path',
        'sort_order',
    ];

    protected $casts = [
        'free' => 'boolean',
    ];
}
