<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Models\StaticPageSeo;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class PageSeoController extends Controller
{
    use ApiResponse;

    /**
     * Retrieve resolved public SEO metadata dictionary for all core static pages.
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->query('lang') ?: app()->getLocale() ?: 'en';

        $data = Cache::remember("api_pages_seo_{$locale}", 3600, function () use ($locale) {
            $dictionary = [];
            foreach (array_keys(StaticPageSeo::PAGES) as $key) {
                $dictionary[$key] = StaticPageSeo::getResolvedPayload($key, $locale);
            }
            return $dictionary;
        });

        return $this->successResponse($data, 'Public static pages SEO retrieved successfully.');
    }

    /**
     * Retrieve resolved public SEO metadata for a single static page.
     */
    public function show(Request $request, string $pageKey): JsonResponse
    {
        $locale = $request->query('lang') ?: app()->getLocale() ?: 'en';

        $data = StaticPageSeo::getResolvedPayload($pageKey, $locale);
        if (!$data) {
            return $this->errorResponse('Page SEO not found.', 404);
        }

        return $this->successResponse($data, "SEO for '{$pageKey}' retrieved successfully.");
    }
}
