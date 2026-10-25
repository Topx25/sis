<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\FooterResource;
use App\Models\FooterSetting;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class FooterController extends Controller
{
    use ApiResponse;

    /**
     * Get the public footer configuration.
     *
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        $setting = FooterSetting::instance();

        return $this->successResponse(
            new FooterResource($setting),
            'Footer configuration retrieved successfully.'
        );
    }
}
