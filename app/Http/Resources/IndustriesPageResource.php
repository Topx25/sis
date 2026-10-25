<?php

namespace App\Http\Resources;

use App\Traits\HasStorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IndustriesPageResource extends JsonResource
{
    use HasStorageUrl;

    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'tagline'     => $this->tagline,
            'headline'    => $this->headline,
            'description' => $this->description,
            'button_text' => $this->button_text,
            'button_link' => $this->button_link ?? '/sectors',
            'image'       => $this->image,
            'image_url'   => $this->formatStorageUrl($this->image),
            'stats'       => $this->stats ?? [],
            'is_active'   => (bool) $this->is_active,
        ];
    }
}
