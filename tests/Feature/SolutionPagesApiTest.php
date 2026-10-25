<?php

namespace Tests\Feature;

use App\Models\SolutionPage;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SolutionPagesApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        // create admin user
        $this->admin = User::factory()->create();
    }

    public function test_can_list_solution_pages()
    {
        SolutionPage::create([
            'title' => 'Test',
            'subtitle' => 'Test subtitle',
            'slug' => 'test',
            'icon' => 'test-icon',
            'description' => 'Test desc',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/solution-pages');

        $response->assertStatus(200)
                 ->assertJsonStructure(['data' => [['id', 'title', 'slug', 'icon', 'description']]]);
    }

    public function test_can_show_solution_page()
    {
        $page = SolutionPage::create([
            'title' => 'Test',
            'subtitle' => 'Test subtitle',
            'slug' => 'test',
            'icon' => 'test-icon',
            'description' => 'Test desc',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->getJson('/api/v1/admin/solution-pages/' . $page->slug);

        $response->assertStatus(200)
                 ->assertJsonPath('data.slug', 'test');
    }

    public function test_can_create_solution_page()
    {
        $data = [
            'title' => 'New Page',
            'subtitle' => 'New subtitle',
            'slug' => 'new-page',
            'icon' => 'new-icon',
            'description' => 'New description',
        ];

        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/solution-pages', $data);

        $response->assertStatus(201);
        $this->assertDatabaseHas('solution_pages', ['slug' => 'new-page']);
    }

    public function test_validates_required_fields_on_create()
    {
        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/solution-pages', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['title', 'slug', 'icon', 'description']);
    }

    public function test_rejects_duplicate_slug_on_create()
    {
        SolutionPage::create([
            'title' => 'Test',
            'subtitle' => 'Test subtitle',
            'slug' => 'test-slug',
            'icon' => 'test-icon',
            'description' => 'Test desc',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->postJson('/api/v1/admin/solution-pages', [
            'title' => 'Another Test',
            'subtitle' => 'Another subtitle',
            'slug' => 'test-slug',
            'icon' => 'test-icon2',
            'description' => 'Desc2',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['slug']);
    }

    public function test_can_update_solution_page()
    {
        $page = SolutionPage::create([
            'title' => 'Test',
            'subtitle' => 'Test subtitle',
            'slug' => 'test',
            'icon' => 'test-icon',
            'description' => 'Test desc',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->putJson('/api/v1/admin/solution-pages/' . $page->slug, [
            'title' => 'Updated Title',
            'subtitle' => 'Updated subtitle',
            'slug' => 'updated-slug',
            'icon' => 'updated-icon',
            'description' => 'Updated desc',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('solution_pages', ['slug' => 'updated-slug', 'icon' => 'updated-icon']);
    }

    public function test_allows_keeping_same_slug_on_update()
    {
        $page = SolutionPage::create([
            'title' => 'Test',
            'subtitle' => 'Test subtitle',
            'slug' => 'test',
            'icon' => 'test-icon',
            'description' => 'Test desc',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->putJson('/api/v1/admin/solution-pages/' . $page->slug, [
            'title' => 'Updated Title',
            'subtitle' => 'Updated subtitle',
            'slug' => 'test',
            'icon' => 'updated-icon',
            'description' => 'Updated desc',
        ]);

        $response->assertStatus(200);
        $this->assertDatabaseHas('solution_pages', ['slug' => 'test']);
    }

    public function test_rejects_existing_slug_on_update()
    {
        SolutionPage::create([
            'title' => 'Test 1',
            'subtitle' => 'Test subtitle 1',
            'slug' => 'test-1',
            'icon' => 'test-icon',
            'description' => 'Test desc',
            'is_active' => true,
        ]);

        $page = SolutionPage::create([
            'title' => 'Test 2',
            'subtitle' => 'Test subtitle 2',
            'slug' => 'test-2',
            'icon' => 'test-icon',
            'description' => 'Test desc',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->putJson('/api/v1/admin/solution-pages/' . $page->slug, [
            'title' => 'Updated Title',
            'subtitle' => 'Updated subtitle',
            'slug' => 'test-1', // existing slug
            'icon' => 'updated-icon',
            'description' => 'Updated desc',
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['slug']);
    }

    public function test_can_delete_solution_page()
    {
        $page = SolutionPage::create([
            'title' => 'Test',
            'subtitle' => 'Test subtitle',
            'slug' => 'test',
            'icon' => 'test-icon',
            'description' => 'Test desc',
            'is_active' => true,
        ]);

        $response = $this->actingAs($this->admin)->deleteJson('/api/v1/admin/solution-pages/' . $page->slug);

        $response->assertStatus(200);
        $this->assertDatabaseMissing('solution_pages', ['slug' => 'test']);
    }

    public function test_unauthorized_user_cannot_access()
    {
        $response = $this->getJson('/api/v1/admin/solution-pages');
        $response->assertStatus(401);
    }
}
