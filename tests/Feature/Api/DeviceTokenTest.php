<?php

namespace Tests\Feature\Api;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DeviceTokenTest extends TestCase
{
    use RefreshDatabase;

    public function test_device_api_is_open_when_no_token_is_configured(): void
    {
        config()->set('instazine.device_token', '');

        $this->get('/api/ping')->assertOk()->assertSee('PONG');
    }

    public function test_device_api_rejects_missing_or_wrong_tokens(): void
    {
        config()->set('instazine.device_token', 'secret-token');

        foreach (['/api/ping', '/api/content', '/api/pic?url=banners/header.bmp'] as $url) {
            $this->getJson($url)->assertUnauthorized();
            $this->getJson($url, ['X-Device-Token' => 'wrong'])->assertUnauthorized();
        }

        $this->postJson('/api/ping', ['Message' => 'hello'])->assertUnauthorized();
        $this->assertDatabaseCount('Health', 0);
    }

    public function test_device_api_accepts_the_configured_token(): void
    {
        config()->set('instazine.device_token', 'secret-token');

        $this->get('/api/ping', ['X-Device-Token' => 'secret-token'])->assertOk();
        $this->postJson('/api/ping', ['Message' => 'hello'], ['X-Device-Token' => 'secret-token'])
            ->assertCreated();
        $this->assertDatabaseCount('Health', 2);
    }
}
