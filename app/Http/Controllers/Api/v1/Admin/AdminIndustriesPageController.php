<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\IndustriesPageResource;
use App\Services\IndustryPageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminIndustriesPageController extends Controller
{
    use ApiResponse;

    public function __construct(protected IndustryPageService $service) {}

    public function show(): JsonResponse
    {
        $page = $this->service->getForAdmin();
        return $this->successResponse(new IndustriesPageResource($page), 'Industries page settings retrieved successfully.');
    }

    public function update(Request $request): JsonResponse
    {
        $page = $this->service->getForAdmin();
        $updated = $this->service->update($page, $request->all());

        return $this->successResponse(new IndustriesPageResource($updated), 'Industries page settings updated successfully.');
    }
}
