<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * HealthCheckTest
 *
 * Feature test untuk Modul 6: Healthcheck
 * FR-25: GET /health endpoint
 * FR-26: HTTP 200 ketika DB terhubung
 * FR-27: HTTP 503 ketika DB tidak terhubung
 */
class HealthCheckTest extends TestCase
{
    use RefreshDatabase;

    // ─── FR-25/FR-26: Endpoint /health mengembalikan 200 ─────────────────────

    public function test_health_endpoint_returns_200_when_database_is_connected(): void
    {
        $response = $this->get('/health');

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'healthy',
        ]);
        $response->assertJsonPath('checks.application', 'ok');
        $response->assertJsonPath('checks.database', 'ok');
    }

    // ─── FR-25: Response memiliki struktur yang benar ─────────────────────────

    public function test_health_endpoint_response_structure(): void
    {
        $response = $this->get('/health');

        $response->assertJsonStructure([
            'status',
            'checks' => [
                'application',
                'database',
                'timestamp',
            ],
        ]);
    }
}
