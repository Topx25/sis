<?php

namespace App\Http\Resources;

use App\Traits\HasStorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeroSectionResource extends JsonResource
{
    use HasStorageUrl;

    public function toArray(Request $request): array
    {
        $img = $this->image;
        $imageUrl = $this->formatStorageUrl($img);

        return [
            'id'          => $this->id,
            'tagline'     => $this->tagline ?: __('messages.hero.tagline'),
            'headline'    => $this->headline ?: __('messages.hero.headline'),
            'subheadline' => $this->subheadline ?: __('messages.hero.subheadline'),
            'image'       => $img,
            'image_path'  => $img,
            'image_url'   => $imageUrl,
            'cta' => [
                'primary'        => $this->cta_primary ?: __('messages.hero.cta_primary'),
                'primary_link'   => $this->cta_primary_link ?? '/projects',
                'secondary'      => $this->cta_secondary ?: __('messages.hero.cta_secondary'),
                'secondary_link' => $this->cta_secondary_link ?? '/contact',
            ],
            'stats'      => $this->stats ?? [],
            'badge_text' => $this->badge_text ?: __('messages.hero.badge_text'),
        ];
    }
}
