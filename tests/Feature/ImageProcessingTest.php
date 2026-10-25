<?php

namespace Tests\Feature;

use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;
use Tests\TestCase;

class ImageProcessingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_uploaded_oversized_image_is_downscaled_to_max_dimensions(): void
    {
        $admin = User::first() ?? User::factory()->create();
        $sector = Sector::first() ?? Sector::create(['name' => 'Energy', 'slug' => 'energy']);

        // Create an image larger than 1200x1200 (e.g. 1800x1200)
        $largeFile = UploadedFile::fake()->image('cover.jpg', 1800, 1200);

        $response = $this->actingAs($admin, 'sanctum')->post('/api/v1/admin/projects', [
            'sector_id' => $sector->id,
            'title' => 'Downscale Test Project',
            'client_name' => 'Test Client',
            'summary' => 'Test Summary',
            'cover_image' => $largeFile,
        ]);

        $response->assertStatus(201);
        $coverPath = $response->json('data.cover_image');
        $this->assertNotEmpty($coverPath);

        // Extract relative storage path
        $relativePath = ltrim(str_replace('/storage/', '', parse_url($coverPath, PHP_URL_PATH) ?? $coverPath), '/');
        $fullPath = storage_path('app/public/' . $relativePath);

        $this->assertFileExists($fullPath);

        $manager = new ImageManager(new Driver());
        $img = $manager->read($fullPath);

        // Aspect ratio 1800x1200 (3:2) downscaled with max 1200x1200 should be 1200x800
        $this->assertLessThanOrEqual(1200, $img->width());
        $this->assertLessThanOrEqual(1200, $img->height());
        $this->assertEquals(1200, $img->width());
        $this->assertEquals(800, $img->height());

        // Clean up
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    public function test_uploaded_small_image_is_not_upsized(): void
    {
        $admin = User::first() ?? User::factory()->create();
        $sector = Sector::first() ?? Sector::create(['name' => 'Energy', 'slug' => 'energy']);

        // Create small image (400x300)
        $smallFile = UploadedFile::fake()->image('small.png', 400, 300);

        $response = $this->actingAs($admin, 'sanctum')->post('/api/v1/admin/projects', [
            'sector_id' => $sector->id,
            'title' => 'Small Image Project',
            'client_name' => 'Test Client',
            'summary' => 'Test Summary',
            'cover_image' => $smallFile,
        ]);

        $response->assertStatus(201);
        $coverPath = $response->json('data.cover_image');
        $relativePath = ltrim(str_replace('/storage/', '', parse_url($coverPath, PHP_URL_PATH) ?? $coverPath), '/');
        $fullPath = storage_path('app/public/' . $relativePath);

        $this->assertFileExists($fullPath);

        $manager = new ImageManager(new Driver());
        $img = $manager->read($fullPath);

        $this->assertEquals(400, $img->width());
        $this->assertEquals(300, $img->height());

        // Clean up
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }

    public function test_multiple_gallery_images_are_all_processed(): void
    {
        $admin = User::first() ?? User::factory()->create();
        $sector = Sector::first() ?? Sector::create(['name' => 'Energy', 'slug' => 'energy']);

        $images = [
            UploadedFile::fake()->image('gallery1.jpg', 1600, 1600),
            UploadedFile::fake()->image('gallery2.webp', 800, 600),
        ];

        $response = $this->actingAs($admin, 'sanctum')->post('/api/v1/admin/projects', [
            'sector_id' => $sector->id,
            'title' => 'Gallery Project',
            'client_name' => 'Test Client',
            'summary' => 'Test Summary',
            'images' => $images,
        ]);

        $response->assertStatus(201);
        $gallery = $response->json('data.images');
        $this->assertCount(2, $gallery);

        foreach ($gallery as $imgUrl) {
            $relativePath = ltrim(str_replace('/storage/', '', parse_url($imgUrl, PHP_URL_PATH) ?? $imgUrl), '/');
            $fullPath = storage_path('app/public/' . $relativePath);
            $this->assertFileExists($fullPath);

            $manager = new ImageManager(new Driver());
            $img = $manager->read($fullPath);
            $this->assertLessThanOrEqual(1200, $img->width());
            $this->assertLessThanOrEqual(1200, $img->height());

            if (file_exists($fullPath)) {
                unlink($fullPath);
            }
        }
    }
}
