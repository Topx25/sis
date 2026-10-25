<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\CoreValuesResource;
use App\Models\CoreValue;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class CoreValuesController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $coreValues = CoreValue::where('is_active', true)->first();

        if (!$coreValues) {
            return $this->errorResponse('Core values section data not found.', 404);
        }

        return $this->successResponse(new CoreValuesResource($coreValues), 'Core values section retrieved successfully.');
    }
}
