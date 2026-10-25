<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreProjectRequest;
use App\Http\Requests\UpdateProjectRequest;
use App\Http\Resources\ProjectResource;
use App\Models\Project;
use App\Services\ProjectService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;

class AdminProjectController extends Controller
{
    use ApiResponse;

    public function __construct(protected ProjectService $projectService) {}

    public function index(): JsonResponse
    {
        // For admin, return all projects with relations loaded
        $projects = Project::with(['sector', 'services', 'images'])->latest()->get();
        return $this->successResponse(ProjectResource::collection($projects), 'Projects retrieved successfully.');
    }

    public function show(Project $project): JsonResponse
    {
        $project->load(['sector', 'services', 'images']);
        return $this->successResponse(new ProjectResource($project), 'Project retrieved successfully.');
    }

    public function store(StoreProjectRequest $request): JsonResponse
    {
        $project = $this->projectService->createProject($request->validated());

        if ($request->has('seo') || $request->hasFile('seo_og_image')) {
            $seoData = $request->input('seo', []);
            if ($request->hasFile('seo_og_image')) {
                if (!is_array($seoData)) $seoData = [];
                $seoData['og_image'] = $request->file('seo_og_image');
            }
            $project->syncSeoMetadata($seoData);
        }

        return $this->createdResponse(new ProjectResource($project), 'Project created successfully.');
    }
    

    public function update(UpdateProjectRequest $request, Project $project): JsonResponse
    {
        $updatedProject = $this->projectService->updateProject($project, $request->validated());

        if ($request->has('seo') || $request->hasFile('seo_og_image')) {
            $seoData = $request->input('seo', []);
            if ($request->hasFile('seo_og_image')) {
                if (!is_array($seoData)) $seoData = [];
                $seoData['og_image'] = $request->file('seo_og_image');
            }
            $updatedProject->syncSeoMetadata($seoData);
        }

        return $this->successResponse(new ProjectResource($updatedProject), 'Project updated successfully.');
    }

    public function destroy(Project $project): JsonResponse
    {
        $this->projectService->deleteProject($project);
        return $this->successResponse(null, 'Project deleted successfully.');
    }
}
