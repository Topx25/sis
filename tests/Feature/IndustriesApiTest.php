<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IndustriesApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_public_can_get_industries_page_data(): void
    {
        $response = $this->getJson('/api/v1/industries');
        
        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'hero',
                    'industries',
                    'stats'
                ]
            ]);
    }

    public function test_admin_can_update_industries_page(): void
    {
        $admin = User::where('email', 'admin@sis-automation.com')->first();
        
        $payload = [
            'headline' => ['en' => 'Updated Headline', 'ar' => 'عنوان محدث'],
        ];

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson('/api/v1/admin/industries-page', $payload);

        $response->assertStatus(200);
        
        $this->assertDatabaseHas('industries_pages', [
            'headline->en' => 'Updated Headline',
        ]);
    }

    public function test_admin_can_update_industries_using_new_put_industries_endpoint(): void
    {
        $admin = User::where('email', 'admin@sis-automation.com')->first();
        
        $payload = [
            'headline' => ['en' => 'Another Headline', 'ar' => 'عنوان آخر'],
        ];

        $response = $this->actingAs($admin, 'sanctum')
            ->putJson('/api/v1/admin/industries', $payload);

        $response->assertStatus(200);

        $this->assertDatabaseHas('industries_pages', [
            'headline->en' => 'Another Headline',
        ]);
    }

    public function test_unauthorized_user_cannot_update_industries(): void
    {
        $payload = [
            'headline' => 'Hacked',
        ];

        $response = $this->putJson('/api/v1/admin/industries', $payload);
        $response->assertStatus(401);
    }
}
