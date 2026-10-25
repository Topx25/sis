<?php

namespace Tests\Feature;

use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CollectionImageUploadErrorTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
        Storage::fake('public');
    }

    public function test_update_sector_with_images()
    {
        $this->withoutExceptionHandling();
        $admin = User::where('email', 'admin@sis-automation.com')->first();
        $sector = Sector::first();
        
        // Ensure sector has an old image
        $sector->update(['image' => 'sectors/old-image.jpg']);

        // Attempt update with POST + _method=PUT
        $response = $this->actingAs($admin, 'sanctum')->postJson('/api/v1/admin/sectors/' . $sector->id, [
            '_method' => 'PUT',
            'title' => ['en' => 'Updated Sector'],
            'image' => UploadedFile::fake()->image('new-image.jpg'),
        ]);

        $response->assertStatus(200);
    }
}
