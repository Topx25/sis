<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Sector;
use App\Models\UseCasesPage;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UseCasesCacheTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        
        UseCasesPage::create([
            'title' => ['en' => 'Original Title', 'ar' => 'Original Title AR'],
            'subtitle' => ['en' => 'Subtitle', 'ar' => 'Subtitle AR'],
            'is_active' => true,
        ]);
    }

    private function createSector()
    {
        return Sector::create([
            'name' => ['en' => 'Sector', 'ar' => 'Sector AR'],
            'slug' => 'sector-' . uniqid(),
            'is_active' => true,
        ]);
    }

    private function createProject()
    {
        return Project::create([
            'title' => ['en' => 'Project', 'ar' => 'Project AR'],
            'slug' => 'project-' . uniqid(),
            'client_name' => ['en' => 'Client', 'ar' => 'Client AR'],
            'summary' => ['en' => 'Summary', 'ar' => 'Summary AR'],
            'description' => ['en' => 'Desc', 'ar' => 'Desc AR'],
            'sector_id' => $this->createSector()->id,
        ]);
    }

    public function test_api_returns_new_data_when_project_is_created()
    {
        $response1 = $this->getJson('/api/v1/use-cases');
        $response1->assertJsonPath('data.projects', []);
        
        $this->assertTrue(Cache::has('api_use_cases_page_en'));

        $this->createProject();

        $response2 = $this->getJson('/api/v1/use-cases');
        $this->assertCount(1, $response2->json('data.projects'));
    }

    public function test_api_returns_updated_data_when_project_is_updated()
    {
        $project = $this->createProject();

        $response1 = $this->getJson('/api/v1/use-cases');
        $response1->assertJsonPath('data.projects.0.title', 'Project');

        $project->update(['title' => ['en' => 'Updated Title', 'ar' => 'Updated Title AR']]);

        $response2 = $this->getJson('/api/v1/use-cases');
        $response2->assertJsonPath('data.projects.0.title', 'Updated Title');
    }

    public function test_api_no_longer_returns_deleted_data()
    {
        $project = $this->createProject();

        $response1 = $this->getJson('/api/v1/use-cases');
        $this->assertCount(1, $response1->json('data.projects'));

        $project->delete();

        $response2 = $this->getJson('/api/v1/use-cases');
        $response2->assertJsonPath('data.projects', []);
    }

    public function test_cache_is_not_invalidated_if_transaction_rolls_back()
    {
        $project = $this->createProject();

        $response1 = $this->getJson('/api/v1/use-cases');
        $this->assertTrue(Cache::has('api_use_cases_page_en'));

        DB::beginTransaction();
        try {
            $project->update(['title' => ['en' => 'Transaction Title', 'ar' => 'Trans AR']]);
            DB::rollBack();
        } catch (\Exception $e) {
            DB::rollBack();
        }

        // Cache should still exist because transaction rolled back
        $this->assertTrue(Cache::has('api_use_cases_page_en'));
        
        // API should return the original data, not the rolled-back data
        $response2 = $this->getJson('/api/v1/use-cases');
        $response2->assertJsonPath('data.projects.0.title', 'Project');
    }
}
