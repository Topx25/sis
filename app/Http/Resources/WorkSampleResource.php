<?php

namespace App\Http\Resources;

use App\Traits\HasStorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class WorkSampleResource extends JsonResource
{
    use HasStorageUrl;

    public function toArray(Request $request): array
    {
        $img = $this->image ?? $this->image_path;
        $imageUrl = $this->formatStorageUrl($img);

        return [
            'id'         => $this->id,
            'slug'       => $this->slug,
            'title'      => $this->title,
            'industry'   => $this->industry ?? $this->category,
            'category'   => $this->category ?? $this->industry,
            'image'      => $img,
            'image_path' => $img,
            'image_url'  => $imageUrl,
            'free'       => (bool) $this->free,
            'blurb'      => $this->blurb ?? $this->body,
            'body'       => $this->body ?? $this->blurb,
            'badge'      => $this->badge,
            'demo_url'   => $this->demo_url ? (str_starts_with($this->demo_url, 'http') ? $this->demo_url : url($this->demo_url)) : null,
            'sort_order' => $this->sort_order,
        ];
    }
}
