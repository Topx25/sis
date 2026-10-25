<?php

namespace Tests\Feature;

use App\Models\Sector;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_admin_can_login_with_valid_credentials(): void
    {
        $response = $this->postJson('/api/v1/admin/login', [
            'email' => 'admin@sis-automation.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'data' => [
                    'user',
                    'access_token',
                    'token_type',
                ],
            ]);
    }

    public function test_unauthenticated_user_cannot_access_protected_admin_routes(): void
    {
        $response = $this->getJson('/api/v1/admin/contact-requests');

        $response->assertStatus(401);
    }

    public function test_authenticated_admin_can_create_project(): void
    {
        $admin = User::where('email', 'admin@sis-automation.com')->first();
        $sector = Sector::first();

        $payload = [
            'sector_id' => $sector->id,
            'title' => 'Smart Refinery Automation System',
            'client_name' => 'EMETHANOL',
            'location' => 'Damietta, Egypt',
            'summary' => 'Complete SCADA & IIoT overhaul for ethanol processing plant.',
            'tags' => ['SCADA', 'Ethanol', 'IIoT'],
        ];

        $response = $this->actingAs($admin, 'sanctum')
            ->postJson('/api/v1/admin/projects', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('data.title', 'Smart Refinery Automation System');

        $this->assertDatabaseHas('projects', [
            'slug' => 'smart-refinery-automation-system',
        ]);
    }
}
