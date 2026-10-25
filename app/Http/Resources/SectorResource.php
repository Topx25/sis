<?php

namespace App\Http\Resources;

use App\Traits\HasStorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SectorResource extends JsonResource
{
    use HasStorageUrl;

    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'slug'           => $this->slug,
            'icon'           => $this->icon,
            'image'          => $this->image,
            'image_url'      => $this->formatStorageUrl($this->image),
            'description'    => $this->description,
            'sort_order'     => $this->sort_order,
            'is_active'      => (bool) $this->is_active,
            'projects_count' => $this->projects_count ?? $this->projects()->count(),
            'relevant_programs' => $this->relevant_programs ?? [],
            'typical_scope'     => $this->typical_scope ?? [],
            'seo'               => method_exists($this->resource, 'getSeoPayload')
                ? $this->getSeoPayload($request->query('lang') ?: 'en')
                : null,
            'seo_raw'           => $this->when(
                $request->is('*/admin/*') && method_exists($this->resource, 'getRawSeoData'),
                fn() => $this->getRawSeoData()
            ),
        ];
    }
}
