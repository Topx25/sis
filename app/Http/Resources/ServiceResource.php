<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ServiceResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'headline'    => $this->headline,
            'slug'        => $this->slug,
            'summary'     => $this->summary,
            'description' => $this->description,
            'icon'        => $this->icon,
            'seo'         => method_exists($this->resource, 'getSeoPayload')
                ? $this->getSeoPayload($request->query('lang') ?: 'en')
                : null,
            'seo_raw'     => $this->when(
                $request->is('*/admin/*') && method_exists($this->resource, 'getRawSeoData'),
                fn() => $this->getRawSeoData()
            ),
        ];
    }
}
