<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProjectGalleryAndMetricsTest extends TestCase
{
    use RefreshDatabase;

    private User $admin;
    private Sector $sector;

    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('public');

        $this->admin = User::factory()->create();
        $this->sector = Sector::create([
            'name' => ['en' => 'Sector Test', 'ar' => 'قطاع تجريبي'],
            'slug' => 'sector-test',
            'is_active' => true,
        ]);
    }

    public function test_updating_project_with_new_image_preserves_existing_images(): void
    {
        // 1. Create a project with an initial gallery image
        $file1 = UploadedFile::fake()->image('gallery1.jpg', 600, 400);

        $createResponse = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/use-cases', [
            'sector_id' => $this->sector->id,
            'title' => 'Test Project',
            'client_name' => 'Acme Corp',
            'summary' => 'Project summary text',
            'images' => [$file1],
            'metrics' => [
                ['value' => '70%'],
                ['label' => 'energy reduction'],
                ['value' => '40%'],
                ['label' => 'dsfe ewsdfesdfzc'],
            ],
        ]);

        $createResponse->assertCreated();
        $projectId = $createResponse->json('data.id');
        $project = Project::with('images')->findOrFail($projectId);

        $this->assertCount(1, $project->images);
        $firstImagePath = $project->images->first()->image_path;
        $firstImageUrl = url('storage/' . $firstImagePath);

        // Verify metrics are healed even if submitted split
        $metrics = $createResponse->json('data.metrics');
        $this->assertCount(2, $metrics);
        $this->assertEquals('70%', $metrics[0]['value']);
        $this->assertEquals('energy reduction', $metrics[0]['label']);
        $this->assertEquals('40%', $metrics[1]['value']);
        $this->assertEquals('dsfe ewsdfesdfzc', $metrics[1]['label']);

        // 2. Update project: keep existing image URL and add a second image
        $file2 = UploadedFile::fake()->image('gallery2.jpg', 600, 400);

        $updateResponse = $this->actingAs($this->admin, 'sanctum')->postJson("/api/v1/admin/use-cases/{$projectId}", [
            '_method' => 'PUT',
            'sector_id' => $this->sector->id,
            'title' => 'Updated Project Title',
            'client_name' => 'Acme Corp',
            'summary' => 'Updated summary text',
            'images' => [$firstImageUrl],
            'gallery_images' => [$file2],
        ]);

        $updateResponse->assertOk();

        // 3. Verify BOTH images exist in the project!
        $project->refresh();
        $project->load('images');

        $this->assertCount(2, $project->images, 'Existing image must not be deleted when adding new image!');

        $paths = $project->images->pluck('image_path')->toArray();
        $this->assertContains($firstImagePath, $paths, 'Original image must remain in the project images!');
    }

    public function test_updating_project_removes_only_explicitly_removed_images(): void
    {
        // 1. Create project with 2 images
        $file1 = UploadedFile::fake()->image('img1.jpg', 400, 400);
        $file2 = UploadedFile::fake()->image('img2.jpg', 400, 400);

        $createResponse = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/use-cases', [
            'sector_id' => $this->sector->id,
            'title' => 'Dual Image Project',
            'client_name' => 'Acme',
            'summary' => 'Dual image summary',
            'images' => [$file1, $file2],
        ]);

        $createResponse->assertCreated();
        $projectId = $createResponse->json('data.id');
        $project = Project::with('images')->findOrFail($projectId);
        $this->assertCount(2, $project->images);

        $keptImagePath = $project->images[0]->image_path;
        $deletedImagePath = $project->images[1]->image_path;
        $keptImageUrl = url('storage/' . $keptImagePath);

        // 2. User deliberately deletes image 2 (only sends keptImageUrl)
        $updateResponse = $this->actingAs($this->admin, 'sanctum')->postJson("/api/v1/admin/use-cases/{$projectId}", [
            '_method' => 'PUT',
            'sector_id' => $this->sector->id,
            'title' => 'Dual Image Project',
            'client_name' => 'Acme',
            'summary' => 'Dual image summary',
            'images' => [$keptImageUrl],
        ]);

        $updateResponse->assertOk();
        $project->refresh();
        $project->load('images');

        $this->assertCount(1, $project->images);
        $this->assertEquals($keptImagePath, $project->images->first()->image_path);
    }

    public function test_legacy_client_sending_both_in_images_array_preserves_existing_image(): void
    {
        $file1 = UploadedFile::fake()->image('img1.jpg', 400, 400);

        $createResponse = $this->actingAs($this->admin, 'sanctum')->postJson('/api/v1/admin/use-cases', [
            'sector_id' => $this->sector->id,
            'title' => 'Legacy Format Project',
            'client_name' => 'Acme',
            'summary' => 'Summary',
            'images' => [$file1],
        ]);

        $projectId = $createResponse->json('data.id');
        $project = Project::with('images')->findOrFail($projectId);
        $originalPath = $project->images->first()->image_path;
        $originalUrl = url('storage/' . $originalPath);

        // Upload another file where both URL and File are passed in the 'images' array
        $file2 = UploadedFile::fake()->image('img2.jpg', 400, 400);

        $updateResponse = $this->actingAs($this->admin, 'sanctum')->postJson("/api/v1/admin/use-cases/{$projectId}", [
            '_method' => 'PUT',
            'sector_id' => $this->sector->id,
            'title' => 'Legacy Format Project',
            'client_name' => 'Acme',
            'summary' => 'Summary',
            'images' => [$originalUrl, $file2],
        ]);

        $updateResponse->assertOk();
        $project->refresh();
        $project->load('images');

        $this->assertCount(2, $project->images, 'Existing image must not be deleted when sent in images array with file!');
        $paths = $project->images->pluck('image_path')->toArray();
        $this->assertContains($originalPath, $paths);
    }
}
