<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkSampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_work_samples(): void
    {
        $this->seed();

        $response = $this->getJson('/api/v1/work-samples');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         '*' => [
                             'id',
                             'slug',
                             'title',
                             'industry',
                             'category',
                             'image',
                             'image_path',
                             'image_url',
                             'free',
                             'blurb',
                             'body',
                             'badge',
                             'demo_url',
                             'sort_order',
                         ]
                     ]
                 ])
                 ->assertJsonFragment(['slug' => 'cement-process-automation', 'free' => true])
                 ->assertJsonFragment(['slug' => 'ai-analysis', 'free' => false]);
    }
}
