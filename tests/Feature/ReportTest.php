<?php

namespace Tests\Feature;

use App\Models\ParkingArea;
use App\Models\ParkingTransaction;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ReportTest
 *
 * Feature test untuk Modul 5: Laporan Parkir
 * FR-20 s/d FR-24
 */
class ReportTest extends TestCase
{
    use RefreshDatabase;

    // ─── FR-20: Halaman okupansi ──────────────────────────────────────────────

    public function test_occupancy_report_is_accessible(): void
    {
        $response = $this->get(route('reports.occupancy'));
        $response->assertStatus(200);
    }

    // ─── FR-21: Daftar kendaraan aktif ───────────────────────────────────────

    public function test_active_vehicles_report_shows_active_transactions(): void
    {
        $area = ParkingArea::factory()->create();

        $transaction = ParkingTransaction::factory()->create([
            'parking_area_id' => $area->id,
            'status'          => 'active',
        ]);

        $response = $this->get(route('reports.active'));

        $response->assertStatus(200);
        $response->assertSee($transaction->license_plate);
    }

    // ─── FR-22: Riwayat transaksi ─────────────────────────────────────────────

    public function test_history_report_shows_completed_transactions(): void
    {
        $area = ParkingArea::factory()->create();

        $completed = ParkingTransaction::factory()->create([
            'parking_area_id' => $area->id,
            'status'          => 'completed',
            'check_out_at'    => Carbon::now(),
            'fee'             => 2000,
        ]);

        $active = ParkingTransaction::factory()->create([
            'parking_area_id' => $area->id,
            'status'          => 'active',
        ]);

        $response = $this->get(route('reports.history'));

        $response->assertStatus(200);
        $response->assertSee($completed->license_plate);
        // Transaksi aktif tidak muncul di riwayat selesai
        $response->assertDontSee($active->license_plate);
    }

    // ─── FR-23/FR-24: Laporan pendapatan ─────────────────────────────────────

    public function test_revenue_report_calculates_total_from_completed_only(): void
    {
        $area = ParkingArea::factory()->create();

        // Transaksi selesai
        ParkingTransaction::factory()->create([
            'parking_area_id' => $area->id,
            'status'          => 'completed',
            'check_out_at'    => Carbon::now(),
            'fee'             => 5_000,
        ]);

        // Transaksi aktif — tidak boleh dihitung sebagai pendapatan
        ParkingTransaction::factory()->create([
            'parking_area_id' => $area->id,
            'status'          => 'active',
            'fee'             => null,
        ]);

        $response = $this->get(route('reports.revenue'));

        $response->assertStatus(200);
        // Hanya Rp5.000 yang terhitung (dari transaksi selesai)
        $response->assertSee('5.000');
    }

    // ─── FR-24: Filter tanggal di revenue ────────────────────────────────────

    public function test_revenue_report_respects_date_filter(): void
    {
        $area = ParkingArea::factory()->create();

        // Transaksi kemarin
        ParkingTransaction::factory()->create([
            'parking_area_id' => $area->id,
            'status'          => 'completed',
            'check_out_at'    => Carbon::yesterday(),
            'fee'             => 10_000,
        ]);

        // Transaksi hari ini
        ParkingTransaction::factory()->create([
            'parking_area_id' => $area->id,
            'status'          => 'completed',
            'check_out_at'    => Carbon::now(),
            'fee'             => 2_000,
        ]);

        // Filter hanya hari ini
        $today = Carbon::today()->toDateString();
        $response = $this->get(route('reports.revenue', [
            'date_from' => $today,
            'date_to'   => $today,
        ]));

        $response->assertStatus(200);
        $response->assertSee('2.000');
    }
}
