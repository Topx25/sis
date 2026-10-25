<?php

namespace App\Http\Resources;

use App\Traits\HasStorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnerResource extends JsonResource
{
    use HasStorageUrl;

    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'name'           => $this->name,
            'logo_url'       => $this->formatStorageUrl($this->logo_path),
            'website_url'    => $this->website_url,
            'type'           => $this->type ?? 'technology_partner',
            'is_featured'    => (bool) $this->is_featured,
            'tagline'        => $this->tagline,
            'description'    => $this->description,
            'glow'           => $this->glow,
            'solutions_link' => $this->solutions_link ?? '/services',
            'sort_order'     => $this->sort_order,
        ];
    }
}
