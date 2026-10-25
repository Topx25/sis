<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ItOtDivideResource;
use App\Models\ItOtDivide;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class ItOtController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $itOt = ItOtDivide::where('is_active', true)->first();

        if (!$itOt) {
            return $this->errorResponse('IT-OT divide section data not found.', 404);
        }

        return $this->successResponse(new ItOtDivideResource($itOt), 'IT-OT divide section retrieved successfully.');
    }
}
