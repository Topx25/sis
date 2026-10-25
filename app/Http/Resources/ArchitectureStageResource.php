<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ArchitectureStageResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'    => $this->id,
            'stage' => $this->stage,
            'title' => $this->title,
            'body'  => $this->body,
            'layer' => $this->layer,
            'icon'  => $this->icon,
        ];
    }
}
