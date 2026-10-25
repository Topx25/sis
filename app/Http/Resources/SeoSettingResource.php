<?php

namespace App\Http\Resources;

use App\Traits\HasStorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SeoSettingResource extends JsonResource
{
    use HasStorageUrl;

    public function toArray(Request $request): array
    {
        $locale = $request->query('lang') ?: app()->getLocale() ?: 'en';
        $isAdmin = $request->is('*/admin/*');

        $base = [
            'id' => $this->id,
            'canonical_base_url' => $this->canonical_base_url ?? 'https://sis.iptvpro.org',
            'google_site_verification' => $this->google_site_verification,
            'bing_site_verification' => $this->bing_site_verification,
            'default_noindex' => (bool) ($this->default_noindex ?? false),
            'default_og_image' => $this->default_og_image,
            'default_og_image_url' => $this->formatStorageUrl($this->default_og_image) ?: $this->default_og_image,
            'organization_legal_name' => $this->organization_legal_name,
            'organization_phone' => $this->organization_phone,
            'organization_email' => $this->organization_email,
            'social_links' => $this->social_links ?? [],
        ];

        if ($isAdmin) {
            // For admin dashboard forms, provide raw bilingual translation maps
            return array_merge($base, [
                'site_name' => [
                    'en' => $this->getTranslation('site_name', 'en', false) ?? '',
                    'ar' => $this->getTranslation('site_name', 'ar', false) ?? '',
                ],
                'title_suffix' => [
                    'en' => $this->getTranslation('title_suffix', 'en', false) ?? '',
                    'ar' => $this->getTranslation('title_suffix', 'ar', false) ?? '',
                ],
                'default_meta_description' => [
                    'en' => $this->getTranslation('default_meta_description', 'en', false) ?? '',
                    'ar' => $this->getTranslation('default_meta_description', 'ar', false) ?? '',
                ],
                'default_meta_keywords' => [
                    'en' => $this->getTranslation('default_meta_keywords', 'en', false) ?? '',
                    'ar' => $this->getTranslation('default_meta_keywords', 'ar', false) ?? '',
                ],
            ]);
        }

        // For public API consumers, resolve to the requested locale with graceful English fallback
        return array_merge($base, [
            'site_name' => $this->getTranslation('site_name', $locale, false)
                ?: $this->getTranslation('site_name', 'en', false)
                ?: 'SiS - Superior Integrated Solutions',
            'title_suffix' => $this->getTranslation('title_suffix', $locale, false)
                ?: $this->getTranslation('title_suffix', 'en', false)
                ?: ' | SiS - Superior Integrated Solutions',
            'default_meta_description' => $this->getTranslation('default_meta_description', $locale, false)
                ?: $this->getTranslation('default_meta_description', 'en', false)
                ?: '',
            'default_meta_keywords' => $this->getTranslation('default_meta_keywords', $locale, false)
                ?: $this->getTranslation('default_meta_keywords', 'en', false)
                ?: '',
        ]);
    }
}
