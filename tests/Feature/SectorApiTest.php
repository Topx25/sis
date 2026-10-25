<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Sector;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SectorApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // create admin user
        $this->admin = User::factory()->create();
    }

    public function test_can_list_industries_with_new_fields()
    {
        $sector = Sector::create([
            'name' => 'Oil & Gas',
            'slug' => 'oil-gas',
            'icon' => 'oil',
            'description' => 'Test desc',
            'is_active' => true,
            'relevant_programs' => ['Prog A', 'Prog B'],
            'typical_scope' => ['Scope A']
        ]);

        $response = $this->getJson('/api/v1/industries');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'relevant_programs' => ['Prog A', 'Prog B'],
                     'typical_scope' => ['Scope A']
                 ]);
    }

    public function test_existing_data_returns_empty_arrays()
    {
        // Simulate existing record where relevant_programs and typical_scope are null
        $sector = new Sector();
        $sector->name = 'Test';
        $sector->slug = 'test';
        $sector->icon = 'test';
        $sector->description = 'desc';
        $sector->is_active = true;
        // Directly inserting to DB to bypass fillable casts if needed, but saving normally will make them null if not set
        $sector->save();

        $response = $this->getJson('/api/v1/industries');

        $response->assertStatus(200)
                 ->assertJsonFragment([
                     'relevant_programs' => [],
                     'typical_scope' => []
                 ]);
    }

    public function test_admin_can_create_sector_with_arrays()
    {
        $payload = [
            'name' => 'New Sector',
            'slug' => 'new-sector',
            'icon' => 'icon',
            'description' => 'desc',
            'is_active' => true,
            'relevant_programs' => ['Prog A', 'Prog B'],
            'typical_scope' => ['Scope A', 'Scope B']
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/sectors', $payload);

        $response->assertStatus(201);
        $this->assertDatabaseHas('sectors', [
            'slug' => 'new-sector',
        ]);
        
        $sector = Sector::where('slug', 'new-sector')->first();
        $this->assertEquals(['Prog A', 'Prog B'], $sector->relevant_programs);
        $this->assertEquals(['Scope A', 'Scope B'], $sector->typical_scope);
    }

    public function test_admin_can_update_sector_with_arrays()
    {
        $sector = Sector::create([
            'name' => 'Old Sector',
            'slug' => 'old-sector',
            'icon' => 'icon',
            'description' => 'desc',
            'is_active' => true,
        ]);

        $payload = [
            'name' => 'Updated Sector',
            'slug' => 'old-sector',
            'icon' => 'icon',
            'description' => 'desc',
            'is_active' => true,
            'relevant_programs' => ['New Prog'],
            'typical_scope' => ['New Scope']
        ];

        $response = $this->actingAs($this->admin)->putJson('/api/v1/admin/sectors/' . $sector->id, $payload);

        $response->assertStatus(200);
        $sector->refresh();
        $this->assertEquals(['New Prog'], $sector->relevant_programs);
        $this->assertEquals(['New Scope'], $sector->typical_scope);
    }

    public function test_validation_rejects_non_array_for_programs()
    {
        $payload = [
            'name' => 'Bad Sector',
            'slug' => 'bad-sector',
            'icon' => 'icon',
            'description' => 'desc',
            'is_active' => true,
            'relevant_programs' => 'Not an array',
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/sectors', $payload);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['relevant_programs']);
    }
}
