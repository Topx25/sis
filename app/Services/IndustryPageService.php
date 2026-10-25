<?php

namespace App\Services;

use App\Models\IndustriesPage;
use App\Models\Sector;
use Illuminate\Support\Facades\Cache;

class IndustryPageService
{
    public function getPageData(): array
    {
        $page = IndustriesPage::where('is_active', true)->first();

        $sectors = Sector::where('is_active', true)
            ->orderBy('sort_order')
            ->get();

        return [
            'hero' => $page,
            'industries' => $sectors,
            'stats' => $page ? $page->stats : [],
        ];
    }

    public function getForAdmin(): IndustriesPage
    {
        return IndustriesPage::firstOrCreate(['id' => 1], [
            'tagline' => 'INDUSTRY EXPERTISE',
            'headline' => 'Solutions Tailored for Every Industry',
            'description' => 'Deep domain expertise and proven experience.',
            'is_active' => true,
        ]);
    }

    public function update(IndustriesPage $page, array $data): IndustriesPage
    {
        $page->fill($data);
        $page->save();

        Cache::forget('api_industries_page_en');
        Cache::forget('api_industries_page_ar');

        return $page;
    }
}
