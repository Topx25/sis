<?php

namespace App\Http\Controllers\Api\v1\Public;

use App\Http\Controllers\Controller;
use App\Http\Resources\ProjectResource;
use App\Services\ProjectService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    use ApiResponse;

    public function __construct(protected ProjectService $projectService) {}

    public function index(Request $request): JsonResponse
    {
        $sectorSlug = $request->query('sector');
        $search = $request->query('search');
        $perPage = min(max((int) $request->query('per_page', 10), 1), 100);

        $projects = $this->projectService->getPaginatedProjects($sectorSlug, $search, $perPage);

        return $this->successResponse([
            'projects' => ProjectResource::collection($projects),
            'pagination' => [
                'total' => $projects->total(),
                'count' => $projects->count(),
                'per_page' => $projects->perPage(),
                'current_page' => $projects->currentPage(),
                'total_pages' => $projects->lastPage(),
            ],
        ], 'Projects retrieved successfully.');
    }

    public function show(string $slug): JsonResponse
    {
        $project = $this->projectService->getProjectBySlug($slug);
        return $this->successResponse(new ProjectResource($project), 'Project details retrieved successfully.');
    }
}
