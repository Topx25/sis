<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ClientTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_fetch_clients_list(): void
    {
        $this->seed();
        $response = $this->getJson('/api/v1/clients');

        $response->assertStatus(200)
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'src',
                             'logo_url',
                             'sort_order',
                         ]
                     ]
                 ])
                 ->assertJsonFragment(['name' => 'Delta'])
                 ->assertJsonFragment(['name' => 'Henkel']);
    }
}
