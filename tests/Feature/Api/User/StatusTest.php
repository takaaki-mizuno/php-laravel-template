<?php

namespace Tests\Feature\Api\User;

//use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Api\ApiTestCase;

class StatusTest extends ApiTestCase
{
    /**
     * Check /api/status
     *
     * @return void
     */
    public function test_the_status_api_returns_a_successful_response(): void
    {
        $response = $this->get('/api/status');
        $response->assertStatus(200);
    }
}
