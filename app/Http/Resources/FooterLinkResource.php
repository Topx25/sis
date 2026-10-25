<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FooterLinkResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $data = [
            'id' => $this->id,
            'category' => $this->category,
            'type' => $this->type,
            'label' => $this->label,
            'url' => $this->url,
            'target' => $this->target ?? '_self',
            'sort_order' => (int) $this->sort_order,
        ];

        if ($this->category === 'contact' || !empty($this->icon)) {
            $data['icon'] = $this->icon;
        }

        if (!empty($this->metadata)) {
            $data['metadata'] = $this->metadata;
        }

        // Include administrative fields if requested or in admin context
        if ($request->is('*/admin/*') || $request->user()) {
            $data['is_active'] = (bool) $this->is_active;
            $data['created_at'] = $this->created_at?->toISOString();
            $data['updated_at'] = $this->updated_at?->toISOString();
        }

        return $data;
    }
}
