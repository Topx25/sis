<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class CacheManager
{
    /**
     * Invalidate the Use Cases page cache safely after a database transaction commits.
     */
    public static function invalidateUseCases(): void
    {
        DB::afterCommit(function () {
            Cache::forget('api_use_cases_page_en');
            Cache::forget('api_use_cases_page_ar');
        });
    }

    /**
     * Invalidate global SEO settings caches safely after a database transaction commits.
     */
    public static function invalidateSeoSettings(): void
    {
        DB::afterCommit(function () {
            Cache::forget('api_seo_settings_en');
            Cache::forget('api_seo_settings_ar');
            Cache::forget('api_seo_settings_all');
            Cache::forget('api_pages_seo_en');
            Cache::forget('api_pages_seo_ar');
            Cache::forget('sis_sitemap_xml');
        });
    }

    /**
     * Invalidate cached XML sitemap safely after transaction commits.
     */
    public static function invalidateSitemap(): void
    {
        DB::afterCommit(function () {
            Cache::forget('sis_sitemap_xml');
        });
    }
}
