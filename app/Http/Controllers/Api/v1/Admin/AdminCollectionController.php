<?php

namespace App\Http\Controllers\Api\v1\Admin;

use App\Http\Controllers\Controller;
use App\Http\Resources\ArchitectureStageResource;
use App\Http\Resources\ClientResource;
use App\Http\Resources\PartnerResource;
use App\Http\Resources\PillarResource;
use App\Http\Resources\SectorResource;
use App\Http\Resources\ServiceResource;
use App\Http\Resources\TimelineEventResource;
use App\Http\Resources\WorkSampleResource;
use App\Models\ArchitectureStage;
use App\Models\Client;
use App\Models\Partner;
use App\Models\Pillar;
use App\Models\Sector;
use App\Models\Service;
use App\Models\TimelineEvent;
use App\Models\WorkSample;
use App\Traits\ApiResponse;
use App\Traits\ImageTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminCollectionController extends Controller
{
    use ApiResponse, ImageTrait;

    protected array $collectionMap = [
        'services' => Service::class,
        'sectors' => Sector::class,
        'industries' => Sector::class,
        'partners' => Partner::class,
        'partner-ecosystem' => Partner::class,
        'partner/ecosystem' => Partner::class,
        'clients' => Client::class,
        'timeline' => TimelineEvent::class,
        'pillars' => Pillar::class,
        'work-flow' => ArchitectureStage::class,
        'work-samples' => WorkSample::class,
        'home-hero-slide' => \App\Models\HomeHeroSlide::class,
        'home-why-sis' => \App\Models\HomeWhySis::class,
        'why-sis' => \App\Models\HomeWhySis::class,
    ];

    protected array $resourceMap = [
        'services' => ServiceResource::class,
        'sectors' => SectorResource::class,
        'industries' => SectorResource::class,
        'partners' => PartnerResource::class,
        'partner-ecosystem' => PartnerResource::class,
        'partner/ecosystem' => PartnerResource::class,
        'clients' => ClientResource::class,
        'timeline' => TimelineEventResource::class,
        'pillars' => PillarResource::class,
        'work-flow' => ArchitectureStageResource::class,
        'work-samples' => WorkSampleResource::class,
        'home-hero-slide' => \App\Http\Resources\HomeHeroSlideResource::class,
        'home-why-sis' => \App\Http\Resources\HomeWhySisResource::class,
        'why-sis' => \App\Http\Resources\HomeWhySisResource::class,
    ];

    protected function getModelClass(string $resource)
    {
        return $this->collectionMap[$resource] ?? null;
    }

    protected function transformResource(string $resource, $item)
    {
        if (isset($this->resourceMap[$resource])) {
            $resourceClass = $this->resourceMap[$resource];
            return new $resourceClass($item);
        }
        return $item;
    }

    protected function handleFileUploads(Request $request, string $resource, array &$data, bool $isUpdate = false, $currentItem = null): void
    {
        $fileFields = ['logo_path', 'image', 'image_path', 'logo', 'cover_image', 'src'];

        foreach ($fileFields as $field) {
            if ($request->hasFile($field)) {
                $request->validate([
                    $field => ['file', 'mimes:jpeg,png,jpg,webp', 'max:' . config('image.max_size_kb', 10240)],
                ]);

                $oldPath = $currentItem ? ($currentItem->{$field} ?? null) : null;
                $filePath = $this->uploadImage($request->file($field), "collections/{$resource}", $oldPath);

                $data[$field] = $filePath;

                // Sync field aliases for models with specific column names
                if (in_array($field, ['logo', 'logo_path', 'src'])) {
                    $data['logo_path'] = $filePath;
                    $data['logo'] = $filePath;
                }
                if (in_array($field, ['image', 'image_path', 'src', 'cover_image'])) {
                    $data['image'] = $filePath;
                    $data['image_path'] = $filePath;
                }
                
                // Keep the original field just in case
                $data[$field] = $filePath;
            } elseif ($isUpdate && array_key_exists($field, $data)) {
                // If updating and no valid uploaded file was passed (e.g. they passed a string URL, 
                // an empty array [], or null), remove it from $data to avoid overwriting existing DB columns.
                unset($data[$field]);
            }
        }
        
        // Decode JSON fields (like translatable fields) if they come as strings from FormData
        $jsonFields = ['title', 'headline', 'description', 'name', 'left_title', 'right_subtitle', 'right_title', 'right_description', 'content', 'features', 'content_list', 'relevant_programs', 'typical_scope', 'seo'];
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
    }

    protected function clearCollectionCache(string $resource): void
    {
        // Clear all generic caches related to collections
        \Illuminate\Support\Facades\Cache::forget('api_partners_page_v2_en');
        \Illuminate\Support\Facades\Cache::forget('api_partners_page_v2_ar');
        \Illuminate\Support\Facades\Cache::forget('api_home_page_en');
        \Illuminate\Support\Facades\Cache::forget('api_home_page_ar');
        \Illuminate\Support\Facades\Cache::forget('api_industries_page_en');
        \Illuminate\Support\Facades\Cache::forget('api_industries_page_ar');
        \Illuminate\Support\Facades\Cache::forget("api_collection_{$resource}_en");
        \Illuminate\Support\Facades\Cache::forget("api_collection_{$resource}_ar");
        \Illuminate\Support\Facades\Artisan::call('cache:clear'); // Full clear as a fail-safe since many APIs are cached aggressively
    }

    public function index(string $resource): JsonResponse
    {
        $modelClass = $this->getModelClass($resource);
        if (!$modelClass) {
            return $this->errorResponse("Unknown collection resource: {$resource}", 404);
        }

        // Return all items without any scopes (or apply default sorting if needed)
        // For simple admin dashboard, returning all is usually fine
        if (method_exists($modelClass, 'scopeOrderBy')) {
            // Some models might not have a generic orderBy scope, just get all
            $items = $modelClass::all(); 
        } else {
            // Check if model has sort_order
            $instance = new $modelClass();
            if (\Illuminate\Support\Facades\Schema::hasColumn($instance->getTable(), 'sort_order')) {
                $items = $modelClass::orderBy('sort_order')->get();
            } else {
                $items = $modelClass::all();
            }
        }

        if ($resource === 'timeline') {
            $items = $items->sortBy(function ($event) {
                if (preg_match('/\d+/', (string)$event->year, $matches)) {
                    $num = (int)$matches[0];
                    return $num < 100 ? $num + 2000 : $num;
                }
                return 0;
            })->values();
        }

        if (isset($this->resourceMap[$resource])) {
            $resourceClass = $this->resourceMap[$resource];
            $data = $resourceClass::collection($items);
        } else {
            $data = $items;
        }

        return $this->successResponse($data, ucfirst($resource) . ' retrieved successfully.');
    }

    public function show(string $resource, int $id): JsonResponse
    {
        $modelClass = $this->getModelClass($resource);
        if (!$modelClass) {
            return $this->errorResponse("Unknown collection resource: {$resource}", 404);
        }

        $item = $modelClass::find($id);
        if (!$item) {
            return $this->errorResponse("Item not found in {$resource}.", 404);
        }

        return $this->successResponse(
            $this->transformResource($resource, $item),
            ucfirst($resource) . ' item retrieved successfully.'
        );
    }

    public function store(Request $request, string $resource): JsonResponse
    {
        $modelClass = $this->getModelClass($resource);
        if (!$modelClass) {
            return $this->errorResponse("Unknown collection resource: {$resource}", 404);
        }

        if ($resource === 'sectors') {
            $request->validate([
                'relevant_programs' => 'nullable|array',
                'relevant_programs.*' => 'string',
                'typical_scope' => 'nullable|array',
                'typical_scope.*' => 'string',
            ]);
        }

        $data = $request->except(['_method', '_token']);

        // Convert string booleans from FormData to actual booleans
        foreach ($data as $key => $value) {
            if ($value === 'true') {
                $data[$key] = true;
            } elseif ($value === 'false') {
                $data[$key] = false;
            } elseif ($value === 'null') {
                $data[$key] = null;
            }
        }

        // Auto-generate slug if title/name exists and slug is empty
        if (empty($data['slug'])) {
            if (!empty($data['title'])) {
                $data['slug'] = Str::slug($data['title']);
            } elseif (!empty($data['name'])) {
                $data['slug'] = Str::slug($data['name']);
            }
        }

        $this->handleFileUploads($request, $resource, $data, false);

        $item = $modelClass::create($data);

        if (!empty($data['seo']) && method_exists($item, 'syncSeoMetadata')) {
            $item->syncSeoMetadata($data['seo']);
        }

        $this->clearCollectionCache($resource);

        return $this->createdResponse(
            $this->transformResource($resource, $item->fresh()),
            ucfirst($resource) . ' item created successfully.'
        );
    }

    public function update(Request $request, string $resource, int $id): JsonResponse
    {
        $modelClass = $this->getModelClass($resource);
        if (!$modelClass) {
            return $this->errorResponse("Unknown collection resource: {$resource}", 404);
        }

        $item = $modelClass::find($id);
        if (!$item) {
            return $this->errorResponse("Item not found in {$resource}.", 404);
        }

        if ($resource === 'sectors') {
            $request->validate([
                'relevant_programs' => 'nullable|array',
                'relevant_programs.*' => 'string',
                'typical_scope' => 'nullable|array',
                'typical_scope.*' => 'string',
            ]);
        }

        $data = $request->except(['_method', '_token']);

        // Convert string booleans from FormData to actual booleans
        foreach ($data as $key => $value) {
            if ($value === 'true') {
                $data[$key] = true;
            } elseif ($value === 'false') {
                $data[$key] = false;
            } elseif ($value === 'null') {
                $data[$key] = null;
            }
        }

        $this->handleFileUploads($request, $resource, $data, true, $item);

        $item->update($data);

        if (!empty($data['seo']) && method_exists($item, 'syncSeoMetadata')) {
            $item->syncSeoMetadata($data['seo']);
        }

        $this->clearCollectionCache($resource);

        return $this->successResponse(
            $this->transformResource($resource, $item->fresh()),
            ucfirst($resource) . ' item updated successfully.'
        );
    }

    public function destroy(string $resource, int $id): JsonResponse
    {
        $modelClass = $this->getModelClass($resource);
        if (!$modelClass) {
            return $this->errorResponse("Unknown collection resource: {$resource}", 404);
        }

        $item = $modelClass::find($id);
        if (!$item) {
            return $this->errorResponse("Item not found in {$resource}.", 404);
        }

        $fileFields = ['logo_path', 'image', 'image_path', 'logo', 'cover_image', 'src'];
        foreach ($fileFields as $f) {
            if (!empty($item->{$f})) {
                $this->deleteImage($item->{$f});
            }
        }

        $item->delete();

        $this->clearCollectionCache($resource);

        return $this->successResponse(null, ucfirst($resource) . ' item deleted successfully.');
    }
}
