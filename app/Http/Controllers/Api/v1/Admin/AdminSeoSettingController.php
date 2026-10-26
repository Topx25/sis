<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\UpdateSeoSettingRequest;
use App\Http\Resources\SeoSettingResource;
use App\Models\SeoSetting;
use App\Traits\ApiResponse;
use App\Traits\ImageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;

class AdminSeoSettingController extends Controller
{
    use ApiResponse, ImageTrait;

    /**
     * Retrieve complete editable SEO settings for the administrative dashboard.
     */
    public function show(): JsonResponse
    {
        $setting = SeoSetting::instance();

        return $this->successResponse(
            new SeoSettingResource($setting),
            'SEO settings retrieved successfully.'
        );
    }

    /**
     * Update global SEO settings.
     */
    public function update(UpdateSeoSettingRequest $request): JsonResponse
    {
        $setting = SeoSetting::instance();
        $data = $request->validated();

        // 1. Process Translatable Fields (Support both arrays and JSON-encoded strings from FormData)
        $translatableFields = ['site_name', 'title_suffix', 'default_meta_description', 'default_meta_keywords'];
        foreach ($translatableFields as $field) {
            if ($request->has($field)) {
                $raw = $request->input($field);
                if (is_string($raw)) {
                    $decoded = json_decode($raw, true);
                    if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                        $raw = $decoded;
                    }
                }

                if (is_array($raw)) {
                    $setting->setTranslations($field, array_filter($raw, fn($v) => $v !== null && $v !== ''));
                }
            }
        }

        // 2. Process Social Links
        if ($request->has('social_links')) {
            $rawSocial = $request->input('social_links');
            if (is_string($rawSocial)) {
                $decoded = json_decode($rawSocial, true);
                if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                    $rawSocial = $decoded;
                }
            }
            if (is_array($rawSocial)) {
                $setting->social_links = array_values(array_filter($rawSocial, fn($v) => !empty($v)));
            }
        }

        // 3. Process Default OG Image (File upload, removal, or path update)
        if ($request->hasFile('default_og_image')) {
            /** @var UploadedFile $file */
            $file = $request->file('default_og_image');
            $setting->default_og_image = $this->uploadImage($file, 'seo', $setting->default_og_image);
        } elseif ($request->boolean('remove_default_og_image')) {
            if ($setting->default_og_image) {
                $this->deleteImage($setting->default_og_image);
                $setting->default_og_image = null;
            }
        } elseif ($request->filled('default_og_image') && is_string($request->input('default_og_image'))) {
            $setting->default_og_image = $request->input('default_og_image');
        }

        // 4. Process Scalar Directives
        if ($request->has('canonical_base_url')) {
            $setting->canonical_base_url = rtrim($request->input('canonical_base_url') ?: config('app.url'), '/');
        }

        if ($request->has('google_site_verification')) {
            $setting->google_site_verification = $request->input('google_site_verification');
        }

        if ($request->has('bing_site_verification')) {
            $setting->bing_site_verification = $request->input('bing_site_verification');
        }

        if ($request->has('default_noindex')) {
            $setting->default_noindex = $request->boolean('default_noindex');
        }

        if ($request->has('organization_legal_name')) {
            $setting->organization_legal_name = $request->input('organization_legal_name');
        }

        if ($request->has('organization_phone')) {
            $setting->organization_phone = $request->input('organization_phone');
        }

        if ($request->has('organization_email')) {
            $setting->organization_email = $request->input('organization_email');
        }

        $setting->save();

        return $this->successResponse(
            new SeoSettingResource($setting),
            'SEO settings updated successfully.'
        );
    }
}
