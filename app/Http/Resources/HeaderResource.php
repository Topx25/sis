<?php

namespace App\Http\Resources;

use App\Models\HeaderSetting;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class HeaderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'             => $this->id,
            'logo_path'      => $this->logo_path,
            'logo_url'       => $this->logo_url,
            'nav_links'      => $this->nav_links ?? HeaderSetting::defaultNavLinks(),
            'button_text'    => $this->button_text ?? 'Get in Touch',
            'button_link'    => $this->button_link ?? '/contact',
            'button_enabled' => (bool) ($this->button_enabled ?? true),
            'updated_at'     => $this->updated_at,
        ];
    }
}
