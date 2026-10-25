<?php

namespace App\Services;

use App\Models\Project;
use App\Traits\ImageTrait;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Str;

class ProjectService
{
    use ImageTrait;

    public function getPaginatedProjects(?string $sectorSlug = null, ?string $search = null, int $perPage = 10): LengthAwarePaginator
    {
        $query = Project::with(['sector', 'services', 'images'])->latest();

        if ($sectorSlug) {
            $query->whereHas('sector', fn ($q) => $q->where('slug', $sectorSlug));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('client_name', 'like', "%{$search}%")
                  ->orWhere('summary', 'like', "%{$search}%");
            });
        }

        return $query->paginate($perPage);
    }

    public function getProjectBySlug(string $slug): Project
    {
        return Project::with(['sector', 'services', 'images'])->where('slug', $slug)->firstOrFail();
    }

    public function createProject(array $data): Project
    {
        $data['slug'] = Str::slug($data['title']);

        if (isset($data['cover_image']) && $data['cover_image'] instanceof \Illuminate\Http\UploadedFile) {
            $data['cover_image'] = $this->uploadImage($data['cover_image'], 'projects');
        }

        if (isset($data['metrics']) && is_array($data['metrics'])) {
            $data['metrics'] = Project::healMetricsList($data['metrics']);
        }

        $project = Project::create($data);

        if (!empty($data['service_ids'])) {
            $project->services()->sync($data['service_ids']);
        }

        $newFiles = collect();
        foreach (['images', 'gallery_images', 'gallery_files'] as $key) {
            if (request()->hasFile($key)) {
                $files = request()->file($key);
                if (is_array($files)) {
                    $newFiles = $newFiles->merge($files);
                } elseif ($files instanceof \Illuminate\Http\UploadedFile) {
                    $newFiles->push($files);
                }
            }
            if (!empty($data[$key]) && is_array($data[$key])) {
                foreach ($data[$key] as $item) {
                    if ($item instanceof \Illuminate\Http\UploadedFile) {
                        $newFiles->push($item);
                    }
                }
            }
        }

        foreach ($newFiles->unique() as $image) {
            $path = $this->uploadImage($image, 'projects/gallery');
            $project->images()->create(['image_path' => $path]);
        }

        return $project->load(['sector', 'services', 'images']);
    }

    public function updateProject(Project $project, array $data): Project
    {
        if (isset($data['title'])) {
            $data['slug'] = Str::slug($data['title']);
        }

        if (array_key_exists('cover_image', $data)) {
            if ($data['cover_image'] instanceof \Illuminate\Http\UploadedFile) {
                $data['cover_image'] = $this->uploadImage($data['cover_image'], 'projects', $project->cover_image);
            } else {
                // Prevent erasing the existing cover image if the frontend doesn't upload a new one
                unset($data['cover_image']);
            }
        }

        if (isset($data['metrics']) && is_array($data['metrics'])) {
            $data['metrics'] = Project::healMetricsList($data['metrics']);
        }

        $project->update($data);

        if (isset($data['service_ids'])) {
            $project->services()->sync($data['service_ids']);
        }

        $hasImagePayload = array_key_exists('images', $data)
            || request()->has('images')
            || request()->hasFile('images')
            || request()->has('gallery_images')
            || request()->hasFile('gallery_images')
            || request()->has('gallery_files')
            || request()->hasFile('gallery_files');

        if ($hasImagePayload) {
            $project->load('images');

            // Retrieve kept string URLs from both request inputs and data array
            // (request()->input('images') preserves string URLs even if Symfony merged files into request data)
            $keptUrls = collect(request()->input('images', []))
                ->merge(collect($data['images'] ?? []))
                ->filter(fn($img) => is_string($img) && !empty(trim($img)))
                ->unique()
                ->values()
                ->toArray();

            // Find old images that are NOT in the kept URLs
            $imagesToDelete = $project->images->filter(function ($oldImage) use ($keptUrls) {
                $cleanOld = ltrim(str_replace('\\', '/', $oldImage->image_path ?? ''), '/');
                if (empty($cleanOld)) return false;

                return !collect($keptUrls)->contains(function ($url) use ($cleanOld) {
                    $cleanUrl = ltrim(str_replace('\\', '/', parse_url($url, PHP_URL_PATH) ?? $url), '/');
                    return str_ends_with($cleanUrl, $cleanOld) || str_ends_with($cleanOld, $cleanUrl);
                });
            });

            // Delete the removed images from DB and storage
            foreach ($imagesToDelete as $oldImage) {
                $this->deleteImage($oldImage->image_path);
                $oldImage->delete();
            }

            // Gather all new uploaded files from all possible keys
            $newFiles = collect();
            foreach (['images', 'gallery_images', 'gallery_files'] as $key) {
                if (request()->hasFile($key)) {
                    $files = request()->file($key);
                    if (is_array($files)) {
                        $newFiles = $newFiles->merge($files);
                    } elseif ($files instanceof \Illuminate\Http\UploadedFile) {
                        $newFiles->push($files);
                    }
                }
                if (!empty($data[$key]) && is_array($data[$key])) {
                    foreach ($data[$key] as $item) {
                        if ($item instanceof \Illuminate\Http\UploadedFile) {
                            $newFiles->push($item);
                        }
                    }
                }
            }

            // Store new uploaded files
            foreach ($newFiles->unique() as $image) {
                $path = $this->uploadImage($image, 'projects/gallery');
                $project->images()->create(['image_path' => $path]);
            }
        }

        return $project->fresh(['sector', 'services', 'images']);
    }

    public function deleteProject(Project $project): bool
    {
        $this->deleteImage($project->cover_image);

        foreach ($project->images as $image) {
            $this->deleteImage($image->image_path);
        }

        return $project->delete();
    }
}
