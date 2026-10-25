<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmpoweredDomainResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'tagline'     => $this->tagline,
            'headline'    => $this->headline,
            'subheadline' => $this->subheadline,
            'domains'     => $this->domains ?? [],
        ];
    }
}
