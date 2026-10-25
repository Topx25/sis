<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\EmpoweredDomainResource;
use App\Models\EmpoweredDomain;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class DomainController extends Controller
{
    use ApiResponse;

    public function index(): JsonResponse
    {
        $domains = EmpoweredDomain::where('is_active', true)->first();

        if (!$domains) {
            return $this->errorResponse('Empowered domains section data not found.', 404);
        }

        return $this->successResponse(new EmpoweredDomainResource($domains), 'Empowered domains section retrieved successfully.');
    }
}
