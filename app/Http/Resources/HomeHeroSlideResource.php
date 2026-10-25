<?php

namespace App\Http\Resources;

use App\Traits\HasStorageUrl;
use Illuminate\Http\Resources\Json\JsonResource;

class HomeHeroSlideResource extends JsonResource
{
    use HasStorageUrl;
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        $url = $this->formatStorageUrl($this->src);
        
        return [
            'id' => $this->id,
            'src' => $this->src,
            'src_url' => $url,
            'image_url' => $url,
            'alt' => $this->alt,
            'title' => $this->getTranslations('title'),
            'description' => $this->getTranslations('description'),
            'sort_order' => $this->sort_order,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
