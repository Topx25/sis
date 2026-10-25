<?php

namespace Tests\Feature;

use App\Models\HeaderSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class HeaderApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();
        $this->admin = User::factory()->create();
    }

    public function test_can_get_public_header_settings()
    {
        $response = $this->getJson('/api/v1/header');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'logo_path',
                         'logo_url',
                         'nav_links',
                         'button_text',
                         'button_link',
                         'button_enabled',
                     ],
                 ]);

        $this->assertEquals('Get in Touch', $response->json('data.button_text'));
        $this->assertEquals('/contact', $response->json('data.button_link'));
        $this->assertTrue($response->json('data.button_enabled'));
        $this->assertCount(5, $response->json('data.nav_links'));
    }

    public function test_admin_can_get_header_settings()
    {
        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/header');

        $response->assertStatus(200)
                 ->assertJsonPath('data.button_text', 'Get in Touch');
    }

    public function test_admin_can_update_header_settings()
    {
        Storage::fake('public');

        $navLinks = [
            [
                'id' => 1,
                'key' => 'home',
                'label' => 'Home Renamed',
                'url' => '/',
                'has_dropdown' => false,
                'is_active' => true,
            ],
            [
                'id' => 2,
                'key' => 'services',
                'label' => 'Solutions',
                'url' => '/services',
                'has_dropdown' => true,
                'is_active' => true,
            ],
        ];

        $payload = [
            'button_text' => 'Talk to Us',
            'button_link' => '/contact-us',
            'button_enabled' => true,
            'nav_links' => $navLinks,
        ];

        $response = $this->actingAs($this->admin)->putJson('/api/v1/admin/header', $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('header_settings', [
            'button_text' => 'Talk to Us',
            'button_link' => '/contact-us',
        ]);

        $publicRes = $this->getJson('/api/v1/header');
        $this->assertEquals('Talk to Us', $publicRes->json('data.button_text'));
        $this->assertCount(2, $publicRes->json('data.nav_links'));
        $this->assertEquals('Home Renamed', $publicRes->json('data.nav_links.0.label'));
    }

    public function test_admin_can_update_logo_image()
    {
        $file = UploadedFile::fake()->image('logo.png', 200, 80);

        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/header', [
            'logo' => $file,
            'button_text' => 'Custom Button',
        ]);

        $response->assertStatus(200);

        $setting = HeaderSetting::instance();
        $this->assertNotNull($setting->logo_path);
        $fullPath = storage_path('app/public/' . $setting->logo_path);
        $this->assertFileExists($fullPath);

        // Clean up
        if (file_exists($fullPath)) {
            unlink($fullPath);
        }
    }
}
