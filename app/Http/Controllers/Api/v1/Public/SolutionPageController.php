<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\SolutionPageResource;
use App\Services\SolutionPageService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class SolutionPageController extends Controller
{
    use ApiResponse;

    public function __construct(protected SolutionPageService $service) {}

    public function index(): JsonResponse
    {
        $cacheKey = "api_solution_pages_index_" . app()->getLocale();

        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () {
            $pages = \App\Models\SolutionPage::where('is_active', true)->get();
            return SolutionPageResource::collection($pages)->resolve();
        });

        return $this->successResponse($data, 'Solution pages retrieved successfully.');
    }

    public function show(string $slug): JsonResponse
    {
        $cacheKey = "api_solution_page_{$slug}_" . app()->getLocale();

        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () use ($slug) {
            $page = $this->service->getBySlug($slug);

            if (!$page) {
                return null;
            }

            return (new SolutionPageResource($page))->resolve();
        });

        if (!$data) {
            return $this->errorResponse(__('messages.responses.not_found'), 404);
        }

        return $this->successResponse($data, 'Solution page retrieved successfully.');
    }

    public function menu(): JsonResponse
    {
        $cacheKey = "api_solution_pages_menu_" . app()->getLocale();

        $data = \Illuminate\Support\Facades\Cache::remember($cacheKey, 3600, function () {
            return \App\Models\SolutionPage::where('is_active', true)
                ->orderBy('id', 'asc')
                ->get()
                ->map(function ($page) {
                    return [
                        'id'          => $page->id,
                        'slug'        => $page->slug,
                        'title'       => $page->title,
                        'name'        => $page->title,
                        'icon'        => $page->icon,
                        'description' => $page->description,
                        'is_active'   => (bool) $page->is_active,
                    ];
                })
                ->values()
                ->toArray();
        });

        return $this->successResponse($data, 'Solution pages menu retrieved successfully.');
    }

    public function digital(): JsonResponse
    {
        return $this->show('digital-solutions');
    }

    public function ai(): JsonResponse
    {
        return $this->show('industrial-ai');
    }

    public function process(): JsonResponse
    {
        return $this->show('process-automation');
    }

    public function industrial(): JsonResponse
    {
        return $this->show('industrial-automation');
    }

    public function generalServices(): JsonResponse
    {
        return $this->show('general-services');
    }
}
