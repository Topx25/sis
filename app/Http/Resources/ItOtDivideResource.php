<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ItOtDivideResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'tagline'     => $this->tagline,
            'headline'    => $this->headline,
            'subheadline' => $this->subheadline,
            'ot'          => $this->ot_data ?? __('messages.it_ot.ot'),
            'it'          => $this->it_data ?? __('messages.it_ot.it'),
        ];
    }
}
