<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\PillarResource;
use App\Models\Pillar;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class PillarController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $pillars = Pillar::orderBy('sort_order')->get();
        return $this->successResponse(PillarResource::collection($pillars), 'Pillars retrieved successfully.');
    }
}
