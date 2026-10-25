<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Http\Resources\SectorResource;
use App\Http\Resources\UseCasesPageResource;
use App\Services\UseCasesPageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class UseCasesController extends Controller
{
    use ApiResponse;

    public function __construct(protected UseCasesPageService $service) {}

    public function index(): JsonResponse
    {
        $cacheKey = 'api_use_cases_page_' . app()->getLocale();

        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () {
            $data = $this->service->getPageData();

            return [
                'header'           => $data['page'] ? json_decode((new UseCasesPageResource($data['page']))->toJson(), true) : null,
                'featured_project' => $data['featured_project'] ? json_decode((new ProjectResource($data['featured_project']))->toJson(), true) : null,
                'projects'         => json_decode(ProjectResource::collection($data['projects'])->toJson(), true),
                'sectors'          => json_decode(SectorResource::collection($data['sectors'])->toJson(), true),
                'pillars'          => $data['page'] ? $data['page']->pillars : [],
            ];
        });

        return $this->successResponse($data, 'Use cases and case studies retrieved successfully.');
    }
}
