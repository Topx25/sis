<?php

namespace App\Services;

use App\Models\SolutionPage;
use Illuminate\Support\Facades\Cache;

class SolutionPageService
{
    protected array $slugAliases = [
        'digital' => 'digital-solutions',
        'ai' => 'industrial-ai',
        'process' => 'process-automation',
        'industrial' => 'industrial-automation',
    ];

    public function resolveSlug(string $slug): string
    {
        return $this->slugAliases[$slug] ?? $slug;
    }

    public function getBySlug(string $slug): ?SolutionPage
    {
        $targetSlug = $this->resolveSlug($slug);

        return SolutionPage::where(function ($q) use ($slug, $targetSlug) {
            $q->where('slug', $targetSlug)
              ->orWhere('slug', $slug);
        })
        ->where('is_active', true)
        ->first();
    }

    public function getAllForAdmin()
    {
        return SolutionPage::all();
    }

    public function getForAdmin(string $slug): SolutionPage
    {
        $targetSlug = $this->resolveSlug($slug);

        return SolutionPage::where('slug', $targetSlug)
            ->orWhere('slug', $slug)
            ->firstOrFail();
    }

    public function update(SolutionPage $page, array $data): SolutionPage
    {
        $page->fill($data);
        $page->save();

        $this->clearCache($page->slug);

        return $page;
    }

    public function create(array $data): SolutionPage
    {
        $page = SolutionPage::create($data);
        $this->clearCache($page->slug);

        return $page;
    }

    public function delete(SolutionPage $page): void
    {
        $slug = $page->slug;
        $page->delete();

        $this->clearCache($slug);
    }

    protected function clearCache(string $slug): void
    {
        Cache::forget("api_solution_page_{$slug}_en");
        Cache::forget("api_solution_page_{$slug}_ar");
        Cache::forget("api_solution_pages_index_en");
        Cache::forget("api_solution_pages_index_ar");
        Cache::forget("api_solution_pages_menu_en");
        Cache::forget("api_solution_pages_menu_ar");
        Cache::forget("api_solution_pages_menu_all");
    }
}
