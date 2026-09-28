<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PingTest extends TestCase
{
    use RefreshDatabase;

    public function test_ping_returns_pong(): void
    {
        $this->get('/api/ping')
            ->assertOk()
            ->assertContent('PONG');

        $this->assertDatabaseHas('Health', ['Message' => 'Okay!']);
    }
}
