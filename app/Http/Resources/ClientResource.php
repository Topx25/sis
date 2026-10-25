<?php

namespace App\Http\Resources;

use App\Traits\HasStorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ClientResource extends JsonResource
{
    use HasStorageUrl;

    public function toArray(Request $request): array
    {
        $logoUrl = $this->formatStorageUrl($this->logo_path);

        return [
            'id'         => $this->id,
            'name'       => $this->name,
            'src'        => $logoUrl,
            'logo_url'   => $logoUrl,
            'sort_order' => $this->sort_order,
        ];
    }
}
