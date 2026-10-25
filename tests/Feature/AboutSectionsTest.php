<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AboutSectionsTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_about_hero_data(): void
    {
        $this->seed();

        $response = $this->getJson('/api/v1/about-hero');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'tagline',
                         'headline',
                         'subheadline',
                         'cta_text',
                         'cta_link',
                         'image',
                         'image_path',
                         'image_url',
                     ]
                 ])
                 ->assertJsonFragment(['headline' => 'Architects of the 4th Industrial Revolution']);
    }

    public function test_can_fetch_it_ot_data(): void
    {
        $this->seed();

        $response = $this->getJson('/api/v1/it-ot');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'tagline',
                         'headline',
                         'subheadline',
                         'ot' => ['title', 'description', 'tags', 'icon'],
                         'it' => ['title', 'description', 'tags', 'icon'],
                     ]
                 ])
                 ->assertJsonFragment(['headline' => 'Bridging the IT-OT Divide']);
    }

    public function test_can_fetch_core_values_data(): void
    {
        $this->seed();

        $response = $this->getJson('/api/v1/core-values');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'tagline',
                         'headline',
                         'subheadline',
                         'principles',
                     ]
                 ])
                 ->assertJsonFragment(['headline' => 'Core Values & Principles']);
    }

    public function test_can_fetch_domains_data(): void
    {
        $this->seed();

        $response = $this->getJson('/api/v1/domains');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'tagline',
                         'headline',
                         'subheadline',
                         'domains',
                     ]
                 ])
                 ->assertJsonFragment(['headline' => 'Industries We Empower']);
    }
}
