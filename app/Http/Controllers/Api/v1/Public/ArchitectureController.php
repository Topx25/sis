<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArchitectureStageResource;
use App\Models\ArchitectureStage;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ArchitectureController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $stages = ArchitectureStage::orderBy('sort_order')->get();
        return $this->successResponse(ArchitectureStageResource::collection($stages), 'Architecture stages retrieved successfully.');
    }
}
