<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeoSettingResource;
use App\Models\SeoSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class SeoSettingController extends Controller
{
    use ApiResponse;

    /**
     * Retrieve public SEO settings and site identity metadata.
     */
    public function index(Request $request): JsonResponse
    {
        $locale = $request->query('lang') ?: app()->getLocale() ?: 'en';

        $setting = Cache::remember("api_seo_settings_{$locale}", 3600, function () {
            return SeoSetting::instance();
        });

        return $this->successResponse(
            new SeoSettingResource($setting),
            'Public SEO settings retrieved successfully.'
        );
    }
}
