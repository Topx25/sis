<?php

namespace App\Services;

use App\Models\Partner;
use App\Models\PartnersPage;
use Illuminate\Support\Facades\Cache;

class PartnersPageService
{
    public function getPageData(): array
    {
        $page = PartnersPage::where('is_active', true)->first() ?? $this->getForAdmin();

        $featuredPartners = Partner::where('is_featured', true)
            ->orderBy('sort_order')
            ->get()
            ->unique(function ($partner) {
                return strtolower(trim($partner->name));
            })
            ->values();

        $featuredNames = $featuredPartners->map(function ($partner) {
            return strtolower(trim($partner->name));
        })->toArray();

        $techPartners = Partner::where('is_featured', false)
            ->orderBy('sort_order')
            ->get()
            ->reject(function ($partner) use ($featuredNames) {
                return in_array(strtolower(trim($partner->name)), $featuredNames);
            })
            ->unique(function ($partner) {
                return strtolower(trim($partner->name));
            })
            ->values();

        return [
            'page' => $page,
            'featured_partners' => $featuredPartners,
            'partners' => $techPartners,
        ];
    }

    public function getForAdmin(): PartnersPage
    {
        return PartnersPage::firstOrCreate(['id' => 1], [
            'title' => 'Our Trusted Partners',
            'subtitle' => 'We collaborate with world-class technology leaders.',
            'is_active' => true,
            'home_subtitle' => 'Our Partners',
            'home_title' => 'Building stronger',
            'home_title_highlight' => 'ecosystems',
            'home_description' => 'We collaborate with world-class technology leaders to deliver innovative, reliable and future-ready solutions for our clients.',
        ]);
    }

    public function update(PartnersPage $page, array $data): PartnersPage
    {
        $page->fill($data);
        $page->save();

        Cache::forget('api_partners_page_v2_en');
        Cache::forget('api_partners_page_v2_ar');

        return $page;
    }
}
