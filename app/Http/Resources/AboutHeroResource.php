<?php

namespace App\Http\Resources;

use App\Traits\HasStorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutHeroResource extends JsonResource
{
    use HasStorageUrl;

    public function toArray(Request $request): array
    {
        $img = $this->image;
        $imageUrl = $this->formatStorageUrl($img);

        return [
            'id'          => $this->id,
            'tagline'     => $this->tagline,
            'headline'    => $this->headline,
            'subheadline' => $this->subheadline,
            'cta_text'    => $this->cta_text,
            'cta_link'    => $this->cta_link,
            'image'       => $img,
            'image_path'  => $img,
            'image_url'   => $imageUrl,
        ];
    }
}
