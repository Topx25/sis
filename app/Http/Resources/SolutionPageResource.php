<?php

namespace App\Http\Resources;

use App\Traits\HasStorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SolutionPageResource extends JsonResource
{
    use HasStorageUrl;

    public function toArray(Request $request): array
    {
        return [
            'id'               => $this->id,
            'slug'             => $this->slug,
            'title'            => $this->title,
            'subtitle'         => $this->subtitle,
            'description'      => $this->description,
            'icon'             => $this->icon,
            'hero_image'       => $this->hero_image,
            'hero_image_url'   => $this->formatStorageUrl($this->hero_image),
            'badges'           => $this->badges ?? [],
            'services'         => collect($this->services ?? [])->map(function ($service) {
                return array_merge([
                    'focus' => 'Expert delivery',
                    'approach' => 'Practical guidance',
                    'outcome' => 'Long-term value'
                ], (array)$service);
            })->toArray(),
            'approach_title'   => $this->approach_title,
            'approach_steps'   => $this->approach_steps ?? [],
            'why_us_title'     => $this->why_us_title,
            'why_us_points'    => $this->why_us_points ?? [],
            'industries_title' => $this->industries_title,
            'industries'       => $this->industries ?? [],
            'cta'              => [
                'headline'    => $this->cta_headline,
                'subheadline' => $this->cta_subheadline,
                'button_text' => $this->cta_button_text,
                'button_link' => $this->cta_button_link ?? '/contact',
            ],
            'is_active'        => (bool) $this->is_active,
            'seo'              => method_exists($this->resource, 'getSeoPayload')
                ? $this->getSeoPayload($request->query('lang') ?: 'en')
                : null,
            'seo_raw'          => $this->when(
                $request->is('*/admin/*') && method_exists($this->resource, 'getRawSeoData'),
                fn() => $this->getRawSeoData()
            ),
        ];
    }
}
