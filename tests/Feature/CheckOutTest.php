<?php

namespace Tests\Feature;

use App\Models\ParkingArea;
use App\Models\ParkingTransaction;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * CheckOutTest
 *
 * Feature test untuk Modul 3 & 4: Check-Out dan Perhitungan Tarif
 * FR-12 s/d FR-19
 */
class CheckOutTest extends TestCase
{
    use RefreshDatabase;

    private ParkingArea $area;

    protected function setUp(): void
    {
        parent::setUp();

        $this->area = ParkingArea::factory()->create([
            'capacity_motorcycle' => 10,
            'capacity_car'        => 10,
            'is_active'           => true,
        ]);
    }

    // ─── FR-12: Halaman check-out dapat diakses ───────────────────────────────

    public function test_check_out_page_is_accessible(): void
    {
        $response = $this->get(route('check-out.index'));
        $response->assertStatus(200);
    }

    // ─── FR-12: Cari transaksi aktif berdasarkan nomor tiket ─────────────────

    public function test_can_find_active_transaction_by_ticket(): void
    {
        $transaction = ParkingTransaction::factory()->create([
            'parking_area_id' => $this->area->id,
            'ticket_number'   => 'PKR-20240101-ABCDE',
            'status'          => 'active',
        ]);

        $response = $this->get(route('check-out.search', ['search' => 'PKR-20240101-ABCDE']));

        $response->assertStatus(200);
        $response->assertSee($transaction->license_plate);
    }

    // ─── FR-13/FR-14/FR-15—FR-19: Proses check-out dengan hitung tarif ───────

    public function test_check_out_calculates_fee_and_completes_transaction(): void
    {
        $checkIn = Carbon::now()->subMinutes(90); // Parkir 90 menit

        $transaction = ParkingTransaction::factory()->create([
            'parking_area_id' => $this->area->id,
            'vehicle_type'    => 'motorcycle',
            'ticket_number'   => 'PKR-20240101-TEST1',
            'check_in_at'     => $checkIn,
            'status'          => 'active',
        ]);

        $response = $this->post(route('check-out.process'), [
            'search' => $transaction->ticket_number,
        ]);

        $response->assertRedirect();

        $transaction->refresh();
        $this->assertSame('completed', $transaction->status);
        $this->assertNotNull($transaction->check_out_at);
        $this->assertNotNull($transaction->fee);
        $this->assertGreaterThan(0, $transaction->fee);
    }

    // ─── FR-19: Parkir dalam grace period → biaya 0 ──────────────────────────

    public function test_check_out_free_within_grace_period(): void
    {
        $checkIn = Carbon::now()->subMinutes(10); // 10 menit — masih grace period

        $transaction = ParkingTransaction::factory()->create([
            'parking_area_id' => $this->area->id,
            'vehicle_type'    => 'motorcycle',
            'ticket_number'   => 'PKR-20240101-FREE1',
            'check_in_at'     => $checkIn,
            'status'          => 'active',
        ]);

        $this->post(route('check-out.process'), ['search' => $transaction->ticket_number]);

        $transaction->refresh();
        $this->assertSame(0, $transaction->fee);
    }

    // ─── FR-12: Transaksi tidak ditemukan ────────────────────────────────────

    public function test_check_out_fails_for_nonexistent_ticket(): void
    {
        $response = $this->post(route('check-out.process'), [
            'search' => 'PKR-TIDAK-ADA',
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
    }
}
