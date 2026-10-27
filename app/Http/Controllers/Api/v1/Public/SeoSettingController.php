<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\SeoSettingResource;
use App\Models\SeoSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SeoSettingController extends Controller
{
    use ApiResponse;

    /**
     * Retrieve public SEO settings and site identity metadata.
     *
     * Resolved fresh per request (not cached) because the canonical base URL is
     * derived from the current host, so the same app serves correct values on
     * any domain. The payload is tiny (one settings row), so this is cheap.
     */
    public function index(Request $request): JsonResponse
    {
        $data = (new SeoSettingResource(SeoSetting::instance()))->resolve($request);

        return $this->successResponse(
            $data,
            'Public SEO settings retrieved successfully.'
        );
    }
}
