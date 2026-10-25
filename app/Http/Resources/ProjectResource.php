<?php

namespace App\Http\Resources;

use App\Traits\HasStorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProjectResource extends JsonResource
{
    use HasStorageUrl;

    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'slug'         => $this->slug,
            'client_name'  => $this->client_name,
            'location'     => $this->location,
            'summary'      => $this->summary,
            'description'  => $this->description,
            'cover_image'  => $this->formatStorageUrl($this->cover_image),
            'images'       => $this->whenLoaded('images', function () {
                return $this->images->map(fn($img) => $this->formatStorageUrl($img->image_path));
            }, []),
            'gallery_limit' => $this->gallery_limit ?? (function () {
                if (is_array($this->tags)) {
                    foreach ($this->tags as $t) {
                        if (is_string($t) && str_starts_with($t, 'gallery_limit:')) {
                            return (int) substr($t, strlen('gallery_limit:'));
                        }
                    }
                }
                return 4;
            })(),
            'tags'         => is_array($this->tags)
                ? array_values(array_filter($this->tags, fn($t) => !is_string($t) || !str_starts_with($t, 'gallery_limit:')))
                : ($this->tags ?? []),
            'is_featured'  => (bool) $this->is_featured,
            'metrics'      => $this->metrics ?? [],
            'scope'        => $this->scope ?? [],
            'technologies' => $this->technologies ?? [],
            'results'      => $this->results ?? [],
            'sector'       => new SectorResource($this->whenLoaded('sector')),
            'services'     => ServiceResource::collection($this->whenLoaded('services')),
            'created_at'   => $this->created_at?->toIso8601String(),
            'seo'          => method_exists($this->resource, 'getSeoPayload')
                ? $this->getSeoPayload($request->query('lang') ?: 'en')
                : null,
            'seo_raw'      => $this->when(
                $request->is('*/admin/*') && method_exists($this->resource, 'getRawSeoData'),
                fn() => $this->getRawSeoData()
            ),
        ];
    }
}
