<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

/**
 * HealthController
 *
 * Modul 6: Healthcheck
 * FR-25: GET /health endpoint
 * FR-26: HTTP 200 ketika aplikasi dan database terhubung
 * FR-27: HTTP 503 ketika database tidak terhubung
 *
 * Digunakan oleh Jenkins untuk Smoke Test (Andrew - DevOps).
 */
class HealthController extends Controller
{
    /**
     * GET /health
     * Mengembalikan status aplikasi dan koneksi database.
     */
    public function __invoke(): JsonResponse
    {
        $checks = [
            'application' => 'ok',
            'database'    => 'unknown',
            'timestamp'   => now()->toIso8601String(),
        ];

        try {
            // Cek koneksi database dengan query ringan
            DB::connection()->getPdo();
            DB::select('SELECT 1');

            $checks['database'] = 'ok';
            $status             = 'healthy';
            $httpStatus         = 200;

        } catch (\Throwable $e) {
            $checks['database'] = 'error';
            $checks['database_message'] = 'Database connection failed.'; // Jangan expose detail internal
            $status      = 'unhealthy';
            $httpStatus  = 503;
        }

        return response()->json([
            'status' => $status,
            'checks' => $checks,
        ], $httpStatus);
    }
}
