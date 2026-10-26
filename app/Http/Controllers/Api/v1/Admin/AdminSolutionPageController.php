<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\SolutionPageResource;
use App\Models\SolutionPage;
use App\Services\SolutionPageService;
use App\Traits\ApiResponse;
use App\Traits\ImageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AdminSolutionPageController extends Controller
{
    use ApiResponse, ImageTrait;

    public function __construct(protected SolutionPageService $service) {}

    public function index(): JsonResponse
    {
        $pages = $this->service->getAllForAdmin();
        return $this->successResponse(SolutionPageResource::collection($pages), 'Solution pages retrieved successfully.');
    }

    public function show(string $slug): JsonResponse
    {
        $page = $this->service->getForAdmin($slug);
        return $this->successResponse(new SolutionPageResource($page), 'Solution page retrieved successfully.');
    }

    protected function prepareData(Request $request, bool $isUpdate = false, ?string $slug = null): array
    {
        $pageId = $isUpdate ? (\App\Models\SolutionPage::where('slug', $slug)->value('id') ?? \App\Models\SolutionPage::where('slug', $this->service->resolveSlug($slug))->value('id')) : null;

        $rules = [
            'title' => 'required',
            'slug' => $isUpdate ? 'nullable|string|unique:solution_pages,slug,' . $pageId : 'required|string|unique:solution_pages,slug',
            'icon' => $isUpdate ? 'nullable' : 'required',
            'description' => $isUpdate ? 'nullable' : 'required',
        ];

        if ($request->hasFile('hero_image')) {
            $rules['hero_image'] = ['file', 'mimes:jpeg,png,jpg,webp', 'max:' . config('image.max_size_kb', 10240)];
        }

        $request->validate($rules);

        $data = $request->except(['_method', '_token']);

        if ($request->hasFile('hero_image')) {
            $data['hero_image'] = $this->uploadImage($request->file('hero_image'), 'solution-pages');
        } elseif ($isUpdate && array_key_exists('hero_image', $data) && is_string($data['hero_image'])) {
            unset($data['hero_image']);
        }

        $jsonFields = ['badges', 'services', 'approach_steps', 'why_us_points', 'industries', 'seo'];
        foreach ($jsonFields as $field) {
            if (isset($data[$field]) && is_string($data[$field])) {
                $decoded = json_decode($data[$field], true);
                if (json_last_error() === JSON_ERROR_NONE) {
                    $data[$field] = $decoded;
                }
            }
        }

        if ($request->hasFile('seo_og_image')) {
            if (!isset($data['seo']) || !is_array($data['seo'])) {
                $data['seo'] = [];
            }
            $data['seo']['og_image'] = $request->file('seo_og_image');
        }

        if (isset($data['cta'])) {
            $cta = is_string($data['cta']) ? json_decode($data['cta'], true) : $data['cta'];
            if (is_array($cta)) {
                if (array_key_exists('headline', $cta)) $data['cta_headline'] = $cta['headline'];
                if (array_key_exists('subheadline', $cta)) $data['cta_subheadline'] = $cta['subheadline'];
                if (array_key_exists('button_text', $cta)) $data['cta_button_text'] = $cta['button_text'];
                if (array_key_exists('button_link', $cta)) $data['cta_button_link'] = $cta['button_link'];
            }
            unset($data['cta']);
        }

        return $data;
    }

    public function store(Request $request): JsonResponse
    {
        $data = $this->prepareData($request, false);
        $page = $this->service->create($data);

        if (!empty($data['seo'])) {
            $page->syncSeoMetadata($data['seo']);
        }

        return $this->createdResponse(new SolutionPageResource($page), 'Solution page created successfully.');
    }

    public function update(Request $request, string $slug): JsonResponse
    {
        $page = $this->service->getForAdmin($slug);
        $data = $this->prepareData($request, true, $slug);
        $updated = $this->service->update($page, $data);

        if (!empty($data['seo'])) {
            $updated->syncSeoMetadata($data['seo']);
        }

        return $this->successResponse(new SolutionPageResource($updated), 'Solution page updated successfully.');
    }

    public function destroy(string $slug): JsonResponse
    {
        $page = $this->service->getForAdmin($slug);
        $this->service->delete($page);

        return $this->successResponse(null, 'Solution page deleted successfully.');
    }
}
