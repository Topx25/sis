<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\WorkSampleResource;
use App\Models\WorkSample;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class WorkSampleController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $workSamples = WorkSample::orderBy('sort_order')->get();
        return $this->successResponse(WorkSampleResource::collection($workSamples), 'Work samples retrieved successfully.');
    }
}
