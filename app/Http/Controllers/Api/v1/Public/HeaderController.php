<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\HeaderResource;
use App\Models\HeaderSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Cache;

class HeaderController extends Controller
{
    use ApiResponse;

    /**
     * Get the public header configuration.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $setting = Cache::rememberForever('api_header_settings', function () {
            return HeaderSetting::instance();
        });

        return $this->successResponse(
            new HeaderResource($setting),
            'Header configuration retrieved successfully.'
        );
    }
}
