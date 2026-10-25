<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AboutSectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'            => $this->id,
            'title'         => $this->title,
            'headline'      => $this->headline,
            'subheadline'   => $this->subheadline,
            'paragraphs'    => $this->paragraphs ?? [],
            'mission'       => $this->mission ?? __('messages.about.mission'),
            'vision'        => $this->vision ?? __('messages.about.vision'),
            'why_choose_us' => $this->why_choose_us ?? __('messages.about.why_choose_us'),
            'why_sis'       => $this->why_choose_us ?? __('messages.about.why_choose_us'),
            'pillars'       => $this->pillars ?? [],
            'principles'    => $this->principles ?? [],
        ];
    }
}
