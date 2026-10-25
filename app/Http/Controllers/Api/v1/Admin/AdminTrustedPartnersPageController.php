<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\PartnersPageResource;
use App\Services\PartnersPageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminTrustedPartnersPageController extends Controller
{
    use ApiResponse;

    public function __construct(protected PartnersPageService $service) {}

    public function show(): JsonResponse
    {
        $page = $this->service->getForAdmin();
        return $this->successResponse(new PartnersPageResource($page), 'Trusted partners page settings retrieved successfully.');
    }

    public function update(Request $request): JsonResponse
    {
        $page = $this->service->getForAdmin();
        $updated = $this->service->update($page, $request->all());

        return $this->successResponse(new PartnersPageResource($updated), 'Trusted partners page settings updated successfully.');
    }
}
