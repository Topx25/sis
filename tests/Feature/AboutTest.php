<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_about_section_data(): void
    {
        $this->seed();

        $response = $this->getJson('/api/v1/vision');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'title',
                         'headline',
                         'subheadline',
                         'paragraphs',
                         'mission' => [
                             'tagline',
                             'title',
                             'description',
                             'icon',
                         ],
                         'vision' => [
                             'tagline',
                             'title',
                             'description',
                             'icon',
                         ],
                         'why_choose_us' => [
                             'title',
                             'description',
                         ],
                         'why_sis' => [
                             'title',
                             'description',
                         ],
                         'pillars',
                         'principles',
                     ]
                 ])
                 ->assertJsonFragment(['headline' => 'Our Mission & Vision']);
    }
}
