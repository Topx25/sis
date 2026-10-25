<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class Partner extends Model
{
    use HasFactory, HasTranslations;

    public array $translatable = [
        'name',
        'tagline',
        'description',
    ];

    protected $fillable = [
        'name',
        'logo_path',
        'website_url',
        'type',
        'is_featured',
        'tagline',
        'description',
        'solutions_link',
        'sort_order',
    ];

    protected $casts = [
        'is_featured' => 'boolean',
    ];

    protected static function booted()
    {
        static::saved(function ($partner) {
            self::clearCache();
        });

        static::deleted(function ($partner) {
            self::clearCache();
        });
    }

    private static function clearCache()
    {
        \Illuminate\Support\Facades\DB::afterCommit(function () {
            \Illuminate\Support\Facades\Cache::forget('api_partners_page_v2_en');
            \Illuminate\Support\Facades\Cache::forget('api_partners_page_v2_ar');
        });
    }
}
