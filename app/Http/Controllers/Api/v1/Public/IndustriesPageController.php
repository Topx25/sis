<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\IndustriesPageResource;
use App\Http\Resources\SectorResource;
use App\Services\IndustryPageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class IndustriesPageController extends Controller
{
    use ApiResponse;

    public function __construct(protected IndustryPageService $service) {}

    public function index(): JsonResponse
    {
        $cacheKey = 'api_industries_page_' . app()->getLocale();

        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () {
            $data = $this->service->getPageData();

            return [
                'hero'       => $data['hero'] ? (new IndustriesPageResource($data['hero']))->resolve() : null,
                'industries' => SectorResource::collection($data['industries'])->resolve(),
                'stats'      => $data['stats'] ?? [],
            ];
        });

        return $this->successResponse($data, 'Industries data retrieved successfully.');
    }
}
