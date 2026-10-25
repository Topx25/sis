<?php

namespace App\Http\Resources;

use App\Models\FooterLink;
use App\Models\FooterSetting;
use App\Traits\HasStorageUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class FooterResource extends JsonResource
{
    use HasStorageUrl;

    /**
     * @param FooterSetting $resource
     */
    public function toArray(Request $request): array
    {
        $setting = $this->resource;

        $currentYear = (int) date('Y');
        $copyrightText = $setting->copyright_text ?: 'Superior Integrated Solutions. All rights reserved.';
        $dynamicYear = (bool) ($setting->dynamic_year ?? true);
        $displayYear = $dynamicYear ? $currentYear : ($setting->metadata['copyright_year'] ?? $currentYear);
        $formattedCopyright = "© {$displayYear} {$copyrightText}";

        // Active navigation links sorted by sort_order
        $navigationLinks = FooterLink::active()
            ->category(FooterLink::CATEGORY_NAVIGATION)
            ->ordered()
            ->get();

        // Active contact and social links sorted by sort_order
        $contactLinks = FooterLink::active()
            ->category(FooterLink::CATEGORY_CONTACT)
            ->ordered()
            ->get();

        $logoUrl = $this->formatStorageUrl($setting->logo_path);

        return [
            'company' => [
                'logo' => $logoUrl,
                'logo_path' => $setting->logo_path,
                'description' => $setting->description ?: 'Bridging OT and IT across the MENA region with industrial automation and IIoT excellence.',
            ],
            'navigation_links' => FooterLinkResource::collection($navigationLinks),
            'contact_links' => FooterLinkResource::collection($contactLinks),
            'floating_contact' => [
                'enabled' => (bool) $setting->floating_contact_enabled,
                'url' => $setting->floating_contact_url ?: 'https://wa.me/201007568651',
                'icon' => $setting->floating_contact_icon ?: 'whatsapp',
                'label' => $setting->floating_contact_label ?: 'Contact us on WhatsApp',
            ],
            'copyright' => [
                'text' => $copyrightText,
                'dynamic_year' => $dynamicYear,
                'year' => $displayYear,
                'formatted' => $formattedCopyright,
            ],
        ];
    }
}
