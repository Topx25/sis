<?php

namespace Tests\Feature;

use App\Models\FooterLink;
use App\Models\FooterSetting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class FooterApiTest extends TestCase
{
    use RefreshDatabase;

    protected User $admin;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed();

        $this->admin = User::where('email', 'admin@sis-automation.com')->first()
            ?? User::factory()->create();
    }

    /**
     * Test public footer retrieval returns expected structure.
     */
    public function test_can_fetch_public_footer_configuration(): void
    {
        $response = $this->getJson('/api/v1/footer');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'company' => [
                        'logo',
                        'logo_path',
                        'description',
                    ],
                    'navigation_links' => [
                        '*' => [
                            'id',
                            'label',
                            'url',
                            'target',
                            'sort_order',
                        ],
                    ],
                    'contact_links' => [
                        '*' => [
                            'id',
                            'type',
                            'label',
                            'url',
                            'target',
                            'sort_order',
                            'icon',
                        ],
                    ],
                    'floating_contact' => [
                        'enabled',
                        'url',
                        'icon',
                        'label',
                    ],
                    'copyright' => [
                        'text',
                        'dynamic_year',
                        'year',
                        'formatted',
                    ],
                ],
            ]);

        $this->assertStringContainsString('Superior Integrated Solutions', $response->json('data.copyright.text'));
    }

    /**
     * Test inactive links are excluded from public footer response.
     */
    public function test_inactive_links_are_excluded_from_public_response(): void
    {
        $inactiveLink = FooterLink::create([
            'category' => FooterLink::CATEGORY_NAVIGATION,
            'type' => 'navigation',
            'label' => 'Hidden Page',
            'url' => '/hidden',
            'sort_order' => 99,
            'is_active' => false,
        ]);

        $response = $this->getJson('/api/v1/footer');

        $response->assertStatus(200);

        $navIds = collect($response->json('data.navigation_links'))->pluck('id')->all();
        $this->assertNotContains($inactiveLink->id, $navIds);

        // Also ensure internal fields are not exposed in public response
        $firstLink = $response->json('data.navigation_links.0');
        $this->assertArrayNotHasKey('is_active', $firstLink);
        $this->assertArrayNotHasKey('created_at', $firstLink);
    }

    /**
     * Test links are returned in correct sort order.
     */
    public function test_public_links_are_ordered_by_sort_order(): void
    {
        FooterLink::query()->delete();

        FooterLink::create([
            'category' => FooterLink::CATEGORY_NAVIGATION,
            'label' => 'Third',
            'url' => '/third',
            'sort_order' => 30,
            'is_active' => true,
        ]);

        FooterLink::create([
            'category' => FooterLink::CATEGORY_NAVIGATION,
            'label' => 'First',
            'url' => '/first',
            'sort_order' => 10,
            'is_active' => true,
        ]);

        FooterLink::create([
            'category' => FooterLink::CATEGORY_NAVIGATION,
            'label' => 'Second',
            'url' => '/second',
            'sort_order' => 20,
            'is_active' => true,
        ]);

        $response = $this->getJson('/api/v1/footer');
        $response->assertStatus(200);

        $labels = collect($response->json('data.navigation_links'))->pluck('label')->all();
        $this->assertEquals(['First', 'Second', 'Third'], $labels);
    }

    /**
     * Test unauthenticated access to admin endpoints is rejected.
     */
    public function test_unauthenticated_user_cannot_access_admin_footer_endpoints(): void
    {
        $this->getJson('/api/v1/admin/footer')->assertStatus(401);
        $this->putJson('/api/v1/admin/footer', ['description' => 'Test'])->assertStatus(401);
        $this->postJson('/api/v1/admin/footer/links', ['label' => 'Test'])->assertStatus(401);
        $this->patchJson('/api/v1/admin/footer/links/1/status')->assertStatus(401);
        $this->patchJson('/api/v1/admin/footer/links/reorder', ['ids' => [1]])->assertStatus(401);
        $this->deleteJson('/api/v1/admin/footer/links/1')->assertStatus(401);
    }

    /**
     * Test admin can fetch complete footer configuration including inactive items.
     */
    public function test_admin_can_fetch_footer_settings_and_all_links(): void
    {
        FooterLink::create([
            'category' => FooterLink::CATEGORY_NAVIGATION,
            'label' => 'Inactive Link',
            'url' => '/inactive',
            'sort_order' => 99,
            'is_active' => false,
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->getJson('/api/v1/admin/footer');

        $response->assertStatus(200)
            ->assertJsonPath('success', true)
            ->assertJsonStructure([
                'data' => [
                    'settings' => [
                        'id',
                        'logo',
                        'description',
                        'copyright_text',
                        'dynamic_year',
                        'floating_contact_enabled',
                        'floating_contact_url',
                    ],
                    'navigation_links',
                    'contact_links',
                    'links',
                ],
            ]);

        // Inactive link is present in admin response
        $allIds = collect($response->json('data.links'))->pluck('label')->all();
        $this->assertContains('Inactive Link', $allIds);
    }

    /**
     * Test admin can update footer settings.
     */
    public function test_admin_can_update_footer_settings(): void
    {
        $payload = [
            'description' => 'Updated SIS description for industrial automation.',
            'copyright_text' => 'Updated Superior Integrated Solutions Ltd.',
            'dynamic_year' => true,
            'floating_contact_enabled' => true,
            'floating_contact_url' => 'https://wa.me/201234567890',
            'floating_contact_icon' => 'whatsapp',
            'floating_contact_label' => 'Chat with our engineers',
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson('/api/v1/admin/footer', $payload);

        $response->assertStatus(200)
            ->assertJsonPath('data.settings.description', 'Updated SIS description for industrial automation.')
            ->assertJsonPath('data.settings.copyright_text', 'Updated Superior Integrated Solutions Ltd.')
            ->assertJsonPath('data.settings.floating_contact_url', 'https://wa.me/201234567890');

        $this->assertDatabaseHas('footer_settings', [
            'copyright_text' => 'Updated Superior Integrated Solutions Ltd.',
            'floating_contact_url' => 'https://wa.me/201234567890',
        ]);
    }

    /**
     * Test logo upload with validation and storage.
     */
    public function test_admin_can_upload_and_remove_footer_logo(): void
    {
        $file = UploadedFile::fake()->image('footer_logo.png', 400, 200);

        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/admin/footer', [
                'logo' => $file,
            ]);

        $response->assertStatus(200);

        $setting = FooterSetting::first();
        $this->assertNotNull($setting->logo_path);
        
        $fullPath = storage_path('app/public/' . $setting->logo_path);
        $this->assertFileExists($fullPath);

        // Test removing logo
        $removeResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/admin/footer', [
                'remove_logo' => true,
            ]);

        $removeResponse->assertStatus(200)
            ->assertJsonPath('data.settings.logo_path', null);

        $setting->refresh();
        $this->assertNull($setting->logo_path);
        $this->assertFileDoesNotExist($fullPath);
    }

    /**
     * Test admin can create a navigation link and contact link.
     */
    public function test_admin_can_create_links(): void
    {
        // 1. Create navigation link
        $navPayload = [
            'category' => FooterLink::CATEGORY_NAVIGATION,
            'type' => 'navigation',
            'label' => 'Products',
            'url' => '/products',
            'target' => '_self',
            'sort_order' => 5,
        ];

        $navResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/admin/footer/links', $navPayload);

        $navResponse->assertStatus(201)
            ->assertJsonPath('data.label', 'Products')
            ->assertJsonPath('data.category', 'navigation');

        $this->assertDatabaseHas('footer_links', [
            'label' => 'Products',
            'category' => 'navigation',
        ]);

        // 2. Create contact link with icon and mailto scheme
        $contactPayload = [
            'category' => FooterLink::CATEGORY_CONTACT,
            'type' => 'email',
            'label' => 'Sales Inquiries',
            'url' => 'mailto:sales@sis-automation.com',
            'target' => '_blank',
            'icon' => 'mail',
            'sort_order' => 3,
        ];

        $contactResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/admin/footer/links', $contactPayload);

        $contactResponse->assertStatus(201)
            ->assertJsonPath('data.label', 'Sales Inquiries')
            ->assertJsonPath('data.icon', 'mail');

        $this->assertDatabaseHas('footer_links', [
            'label' => 'Sales Inquiries',
            'url' => 'mailto:sales@sis-automation.com',
        ]);
    }

    /**
     * Test admin can update an existing link.
     */
    public function test_admin_can_update_link(): void
    {
        $link = FooterLink::first();

        $response = $this->actingAs($this->admin, 'sanctum')
            ->putJson("/api/v1/admin/footer/links/{$link->id}", [
                'label' => 'Updated Home Link',
                'url' => '/home-custom',
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.label', 'Updated Home Link')
            ->assertJsonPath('data.url', '/home-custom');

        $this->assertDatabaseHas('footer_links', [
            'id' => $link->id,
            'label' => 'Updated Home Link',
        ]);
    }

    /**
     * Test admin can toggle active status of a link.
     */
    public function test_admin_can_update_link_status(): void
    {
        $link = FooterLink::first();
        $originalStatus = $link->is_active;

        $response = $this->actingAs($this->admin, 'sanctum')
            ->patchJson("/api/v1/admin/footer/links/{$link->id}/status", [
                'is_active' => !$originalStatus,
            ]);

        $response->assertStatus(200)
            ->assertJsonPath('data.is_active', !$originalStatus);

        $link->refresh();
        $this->assertEquals(!$originalStatus, $link->is_active);
    }

    /**
     * Test admin can reorder links.
     */
    public function test_admin_can_reorder_links(): void
    {
        $links = FooterLink::take(2)->get();
        $id1 = $links[0]->id;
        $id2 = $links[1]->id;

        $response = $this->actingAs($this->admin, 'sanctum')
            ->patchJson('/api/v1/admin/footer/links/reorder', [
                'links' => [
                    ['id' => $id1, 'sort_order' => 99],
                    ['id' => $id2, 'sort_order' => 100],
                ],
            ]);

        $response->assertStatus(200);

        $this->assertDatabaseHas('footer_links', ['id' => $id1, 'sort_order' => 99]);
        $this->assertDatabaseHas('footer_links', ['id' => $id2, 'sort_order' => 100]);
    }

    /**
     * Test deleting a link does NOT delete footer settings.
     */
    public function test_deleting_link_does_not_affect_footer_settings(): void
    {
        $link = FooterLink::first();
        $settingId = FooterSetting::first()->id;

        $response = $this->actingAs($this->admin, 'sanctum')
            ->deleteJson("/api/v1/admin/footer/links/{$link->id}");

        $response->assertStatus(200)
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('footer_links', ['id' => $link->id]);

        // Verify footer settings record is completely intact
        $this->assertDatabaseHas('footer_settings', ['id' => $settingId]);
    }

    /**
     * Test validation rejects unsafe URLs and invalid icons.
     */
    public function test_validation_rejects_unsafe_urls_and_bad_icons(): void
    {
        // Unsafe javascript: url
        $badUrlResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/admin/footer/links', [
                'category' => 'contact',
                'label' => 'Hacker Link',
                'url' => 'javascript:alert(1)',
            ]);

        $badUrlResponse->assertStatus(422)
            ->assertJsonValidationErrors(['url']);

        // HTML injection in icon identifier
        $badIconResponse = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/admin/footer/links', [
                'category' => 'contact',
                'label' => 'Icon Injection',
                'url' => 'https://example.com',
                'icon' => '<script>alert(1)</script>',
            ]);

        $badIconResponse->assertStatus(422)
            ->assertJsonValidationErrors(['icon']);
    }

    /**
     * Test phone (tel:) format is accepted and validated.
     */
    public function test_accepts_valid_phone_format(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
            ->postJson('/api/v1/admin/footer/links', [
                'category' => 'contact',
                'type' => 'phone',
                'label' => 'Hotline',
                'url' => 'tel:+201007568651',
                'icon' => 'phone',
            ]);

        $response->assertStatus(201)
            ->assertJsonPath('data.url', 'tel:+201007568651');
    }
}
