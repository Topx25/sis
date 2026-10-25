<?php

namespace Tests\Feature;

use App\Models\Project;
use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ImageUploadErrorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_update_project_with_images()
    {
        $this->withoutExceptionHandling();
        $admin = User::where('email', 'admin@sis-automation.com')->first();
        $sector = Sector::first();

        // Create a project first
        $project = Project::create([
            'sector_id' => $sector->id,
            'title' => 'Test Project',
            'slug' => 'test-project',
            'client_name' => 'Client',
            'location' => 'Location',
            'summary' => 'Summary',
            'cover_image' => 'projects/old-cover.jpg',
        ]);
        $project->images()->create(['image_path' => 'projects/gallery/old-gallery.jpg']);

        // Attempt update with POST + _method=PUT
        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/projects/' . $project->id, [
            '_method' => 'PUT',
            'title' => 'Updated Project',
            'client_name' => 'Updated Client',
            'location' => 'Updated Location',
            'summary' => 'Updated Summary',
            'cover_image' => UploadedFile::fake()->image('cover.jpg'),
            'images' => [
                'http://localhost/storage/projects/gallery/old-gallery.jpg', // Keep old
                UploadedFile::fake()->image('new-gallery.jpg'), // Add new
            ],
        ]);

        $response->assertStatus(200);
    }
}
