<?php

namespace App\Http\Resources;

use App\Models\FooterLink;
use App\Models\FooterSetting;
use App\Traits\HasStorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FooterAdminResource extends JsonResource
{
    use HasStorageUrl;

    /**
     * @param FooterSetting $resource
     */
    public function toArray(Request $request): array
    {
        $setting = $this->resource;

        $navigationLinks = FooterLink::category(FooterLink::CATEGORY_NAVIGATION)
            ->ordered()
            ->get();

        $contactLinks = FooterLink::category(FooterLink::CATEGORY_CONTACT)
            ->ordered()
            ->get();

        $allLinks = FooterLink::ordered()->get();

        return [
            'settings' => [
                'id' => $setting->id,
                'logo' => $this->formatStorageUrl($setting->logo_path),
                'logo_path' => $setting->logo_path,
                'description' => $setting->description,
                'copyright_text' => $setting->copyright_text,
                'dynamic_year' => (bool) $setting->dynamic_year,
                'floating_contact_enabled' => (bool) $setting->floating_contact_enabled,
                'floating_contact_url' => $setting->floating_contact_url,
                'floating_contact_icon' => $setting->floating_contact_icon,
                'floating_contact_label' => $setting->floating_contact_label,
                'metadata' => $setting->metadata,
                'created_at' => $setting->created_at?->toISOString(),
                'updated_at' => $setting->updated_at?->toISOString(),
            ],
            'navigation_links' => FooterLinkResource::collection($navigationLinks),
            'contact_links' => FooterLinkResource::collection($contactLinks),
            'links' => FooterLinkResource::collection($allLinks),
        ];
    }
}
