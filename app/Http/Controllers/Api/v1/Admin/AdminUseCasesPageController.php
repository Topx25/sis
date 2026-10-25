<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\UseCasesPageResource;
use App\Services\UseCasesPageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminUseCasesPageController extends Controller
{
    use ApiResponse;

    public function __construct(protected UseCasesPageService $service) {}

    public function show(): JsonResponse
    {
        $page = $this->service->getForAdmin();
        return $this->successResponse(new UseCasesPageResource($page), 'Use cases page settings retrieved successfully.');
    }

    public function update(Request $request): JsonResponse
    {
        $page = $this->service->getForAdmin();
        $updated = $this->service->update($page, $request->all());

        return $this->successResponse(new UseCasesPageResource($updated), 'Use cases page settings updated successfully.');
    }
}
