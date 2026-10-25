<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Models\SeoMetadata;
use App\Models\StaticPageSeo;
use App\Support\CacheManager;
use App\Traits\ApiResponse;
use App\Traits\ImageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;

class AdminPageSeoController extends Controller
{
    use ApiResponse, ImageTrait;

    /**
     * Retrieve all core static pages with their raw bilingual SEO metadata.
     */
    public function index(): JsonResponse
    {
        $pages = [];
        foreach (array_keys(StaticPageSeo::PAGES) as $key) {
            $pages[] = StaticPageSeo::getRawSeoData($key);
        }

        return $this->successResponse($pages, 'Core pages SEO metadata retrieved successfully.');
    }

    /**
     * Retrieve raw bilingual SEO metadata for a single static page.
     */
    public function show(string $pageKey): JsonResponse
    {
        $data = StaticPageSeo::getRawSeoData($pageKey);
        if (!$data) {
            return $this->errorResponse('Page SEO configuration not found.', 404);
        }

        return $this->successResponse($data, "SEO metadata for '{$pageKey}' retrieved successfully.");
    }

    /**
     * Update SEO metadata for a specific static page.
     */
    public function update(Request $request, string $pageKey): JsonResponse
    {
        $config = StaticPageSeo::getPage($pageKey);
        if (!$config) {
            return $this->errorResponse("Invalid page key: '{$pageKey}'.", 404);
        }

        $request->validate([
            'canonical_url' => 'nullable|string|max:500',
            'schema_type' => 'nullable|string|max:50',
        ]);

        if ($request->hasFile('og_image')) {
            $request->validate([
                'og_image' => 'file|mimes:jpeg,png,jpg,gif,webp,svg|max:5120',
            ]);
        }

        $pageId = $config['id'];

        /** @var SeoMetadata $metadata */
        $metadata = SeoMetadata::firstOrNew([
            'seoable_type' => 'static_page',
            'seoable_id' => $pageId,
        ]);

        // 1. Process Translatable Fields (Support both associative arrays and JSON strings from FormData)
        $translatable = ['meta_title', 'meta_description', 'og_title', 'og_description'];
        foreach ($translatable as $field) {
            if ($request->has($field)) {
                $raw = $request->input($field);
                if (is_string($raw)) {
                    $decoded = json_decode($raw, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $raw = $decoded;
                    }
                }

                if (is_array($raw)) {
                    $metadata->setTranslations($field, array_filter($raw, fn($v) => $v !== null && $v !== ''));
                } elseif (is_string($raw)) {
                    $metadata->setTranslation($field, 'en', $raw);
                }
            }
        }

        // 2. Process Image (File upload, remove flag, or string path)
        if ($request->hasFile('og_image')) {
            /** @var UploadedFile $file */
            $file = $request->file('og_image');
            $metadata->og_image = $this->uploadImage($file, 'seo', $metadata->og_image);
        } elseif ($request->boolean('remove_og_image')) {
            if ($metadata->og_image) {
                $this->deleteImage($metadata->og_image);
                $metadata->og_image = null;
            }
        } elseif ($request->filled('og_image') && is_string($request->input('og_image'))) {
            $metadata->og_image = $request->input('og_image');
        }

        // 3. Process Directives
        if ($request->has('canonical_url')) {
            $metadata->canonical_url = $request->input('canonical_url') ?: null;
        }

        if ($request->has('is_noindex')) {
            $metadata->is_noindex = $request->boolean('is_noindex');
        }

        if ($request->has('is_nofollow')) {
            $metadata->is_nofollow = $request->boolean('is_nofollow');
        }

        if ($request->has('schema_type')) {
            $metadata->schema_type = $request->input('schema_type') ?: $config['schema_type'];
        }

        // 4. Process Keywords (Stored inside schema_overrides['keywords'])
        if ($request->has('meta_keywords')) {
            $rawKeywords = $request->input('meta_keywords');
            if (is_string($rawKeywords)) {
                $decoded = json_decode($rawKeywords, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $rawKeywords = $decoded;
                }
            }
            $overrides = $metadata->schema_overrides ?? [];
            if (is_array($rawKeywords)) {
                $overrides['keywords'] = [
                    'en' => trim($rawKeywords['en'] ?? ''),
                    'ar' => trim($rawKeywords['ar'] ?? ''),
                ];
            } elseif (is_string($rawKeywords)) {
                $overrides['keywords'] = [
                    'en' => trim($rawKeywords),
                    'ar' => $overrides['keywords']['ar'] ?? '',
                ];
            }
            $metadata->schema_overrides = $overrides;
        }

        $metadata->save();

        // Invalidate public caches & sitemap
        CacheManager::invalidateSeoSettings();

        $freshData = StaticPageSeo::getRawSeoData($pageKey);

        return $this->successResponse($freshData, "SEO metadata for '{$pageKey}' updated successfully.");
    }
}
