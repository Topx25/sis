<?php

namespace App\Services;

use App\Models\Project;
use App\Models\Sector;
use App\Models\UseCasesPage;
use Illuminate\Support\Facades\Cache;

class UseCasesPageService
{
    public function getPageData(): array
    {
        $page = UseCasesPage::where('is_active', true)->first();
        $featuredProject = Project::where('is_featured', true)
            ->with(['sector', 'services', 'images'])
            ->first();

        $projects = Project::where('is_featured', false)
            ->with(['sector', 'services', 'images'])
            ->latest()
            ->take(12)
            ->get();

        $sectors = Sector::where('is_active', true)->get();

        return [
            'page' => $page,
            'featured_project' => $featuredProject,
            'projects' => $projects,
            'sectors' => $sectors,
        ];
    }

    public function getForAdmin(): UseCasesPage
    {
        return UseCasesPage::firstOrCreate(['id' => 1], [
            'title' => 'Projects & Case Studies',
            'subtitle' => 'Real-world automation and digital transformation projects.',
            'is_active' => true,
        ]);
    }

    public function update(UseCasesPage $page, array $data): UseCasesPage
    {
        $page->fill($data);
        $page->save();

        return $page;
    }
}
