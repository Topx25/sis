<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PartnersPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'           => $this->id,
            'title'        => $this->title,
            'subtitle'     => $this->subtitle,
            'icon'                 => $this->icon ?? 'handshake',
            'value_badges'         => $this->value_badges ?? [],
            'is_active'            => (bool) $this->is_active,
            'home_subtitle'        => $this->home_subtitle,
            'home_title'           => $this->home_title,
            'home_title_highlight' => $this->home_title_highlight,
            'home_description'     => $this->home_description,
        ];
    }
}
