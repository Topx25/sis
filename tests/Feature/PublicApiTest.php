<?php

namespace Tests\Feature;

use App\Mail\ContactRequestMail;
use App\Models\Project;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class PublicApiTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed();
    }

    public function test_can_fetch_projects_list(): void
    {
        $response = $this->getJson('/api/v1/projects');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'success',
                'message',
                'data' => [
                    'projects',
                    'pagination',
                ],
            ]);
    }

    public function test_can_fetch_single_project_by_slug(): void
    {
        $project = Project::first();

        $response = $this->getJson('/api/v1/projects/' . $project->slug);

        $response->assertStatus(200)
            ->assertJsonPath('data.title', $project->title);
    }

    public function test_can_fetch_services_list(): void
    {
        $response = $this->getJson('/api/v1/services');

        $response->assertStatus(200)
            ->assertJsonPath('success', true);
    }

    public function test_can_submit_contact_request(): void
    {
        Mail::fake();

        $payload = [
            'name' => 'Engineer Ahmad',
            'email' => 'ahmad@factory.com',
            'phone' => '+201000000000',
            'company_name' => 'Industrial Plant Co.',
            'message' => 'We would like to request a SCADA upgrade quote for our chemical line.',
        ];

        $response = $this->postJson('/api/v1/contact', $payload);

        $response->assertStatus(201)
            ->assertJsonPath('success', true)
            ->assertJsonPath('data.name', 'Engineer Ahmad');

        $this->assertDatabaseHas('contact_requests', [
            'email' => 'ahmad@factory.com',
        ]);

        Mail::assertSent(ContactRequestMail::class, function ($mail) {
            return $mail->contactRequest->email === 'ahmad@factory.com' &&
                   $mail->hasFrom(config('mail.from.address'), 'Engineer Ahmad') &&
                   $mail->hasReplyTo('ahmad@factory.com', 'Engineer Ahmad');
        });
    }
}
