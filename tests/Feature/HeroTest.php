<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HeroTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_hero_section_data(): void
    {
        $this->seed();

        $response = $this->getJson('/api/v1/hero');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'tagline',
                         'headline',
                         'subheadline',
                         'image',
                         'image_path',
                         'image_url',
                         'cta' => [
                             'primary',
                             'primary_link',
                             'secondary',
                             'secondary_link',
                         ],
                         'stats',
                         'badge_text',
                     ]
                 ])
                 ->assertJsonFragment(['headline' => 'Where operational meets information technology']);
    }
}
