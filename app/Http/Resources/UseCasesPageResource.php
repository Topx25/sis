<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UseCasesPageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'title'     => $this->title,
            'subtitle'  => $this->subtitle,
            'cta'       => [
                'text' => $this->cta_text,
                'link' => $this->cta_link ?? '/projects',
            ],
            'stats'     => $this->stats ?? [],
            'pillars'   => $this->pillars ?? [],
            'is_active' => (bool) $this->is_active,
        ];
    }
}
