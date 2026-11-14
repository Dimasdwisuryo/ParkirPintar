<?php

namespace Tests\Feature;

use App\Models\ParkingArea;
use App\Models\ParkingTransaction;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * CheckInTest
 *
 * Feature test untuk Modul 2: Check-In Kendaraan
 * FR-07 s/d FR-11, NFR-02, NFR-03
 */
class CheckInTest extends TestCase
{
    use RefreshDatabase;

    private ParkingArea $area;

    protected function setUp(): void
    {
        parent::setUp();

        $this->area = ParkingArea::factory()->create([
            'capacity_motorcycle' => 5,
            'capacity_car'        => 3,
            'is_active'           => true,
        ]);
    }

    // ─── FR-07/FR-08/FR-09: Halaman form check-in dapat diakses ──────────────

    public function test_check_in_form_is_accessible(): void
    {
        $response = $this->get(route('check-in.create'));
        $response->assertStatus(200);
    }

    // ─── FR-10/FR-11: Check-in berhasil ──────────────────────────────────────

    public function test_successful_motorcycle_check_in(): void
    {
        $response = $this->post(route('check-in.store'), [
            'license_plate'   => 'B 1234 ABC',
            'vehicle_type'    => 'motorcycle',
            'parking_area_id' => $this->area->id,
        ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('parking_transactions', [
            'license_plate' => 'B 1234 ABC',
            'vehicle_type'  => 'motorcycle',
            'status'        => 'active',
        ]);
    }

    public function test_ticket_number_is_generated_on_check_in(): void
    {
        $this->post(route('check-in.store'), [
            'license_plate'   => 'D 9999 ZZ',
            'vehicle_type'    => 'car',
            'parking_area_id' => $this->area->id,
        ]);

        $transaction = ParkingTransaction::where('license_plate', 'D 9999 ZZ')->first();
        $this->assertNotNull($transaction);
        $this->assertStringStartsWith('PKR-', $transaction->ticket_number);
    }

    // ─── FR-10: Tolak jika kapasitas penuh ───────────────────────────────────

    public function test_check_in_rejected_when_capacity_full(): void
    {
        // Isi semua slot motor (kapasitas = 5)
        ParkingTransaction::factory()->count(5)->create([
            'parking_area_id' => $this->area->id,
            'vehicle_type'    => 'motorcycle',
            'status'          => 'active',
        ]);

        $response = $this->post(route('check-in.store'), [
            'license_plate'   => 'B 5678 XYZ',
            'vehicle_type'    => 'motorcycle',
            'parking_area_id' => $this->area->id,
        ]);

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseMissing('parking_transactions', ['license_plate' => 'B 5678 XYZ']);
    }

    // ─── NFR-03: Cegah tiket aktif ganda per plat ────────────────────────────

    public function test_duplicate_active_plate_is_rejected(): void
    {
        // Check-in pertama
        ParkingTransaction::factory()->create([
            'license_plate'   => 'B 1111 AA',
            'vehicle_type'    => 'motorcycle',
            'parking_area_id' => $this->area->id,
            'status'          => 'active',
        ]);

        // Check-in kedua dengan plat sama
        $response = $this->post(route('check-in.store'), [
            'license_plate'   => 'B 1111 AA',
            'vehicle_type'    => 'motorcycle',
            'parking_area_id' => $this->area->id,
        ]);

        $response->assertSessionHas('error');
        // Hanya ada 1 transaksi aktif untuk plat ini
        $count = ParkingTransaction::where('license_plate', 'B 1111 AA')->where('status', 'active')->count();
        $this->assertSame(1, $count);
    }

    // ─── NFR-08: Validasi format plat ────────────────────────────────────────

    public function test_invalid_license_plate_format_is_rejected(): void
    {
        $response = $this->post(route('check-in.store'), [
            'license_plate'   => 'INVALID PLATE!!!',
            'vehicle_type'    => 'motorcycle',
            'parking_area_id' => $this->area->id,
        ]);

        $response->assertSessionHasErrors('license_plate');
    }

    // ─── FR-01/FR-10: Area tidak aktif ditolak ───────────────────────────────

    public function test_check_in_rejected_for_inactive_area(): void
    {
        $inactiveArea = ParkingArea::factory()->create([
            'capacity_motorcycle' => 10,
            'is_active'           => false,
        ]);

        $response = $this->post(route('check-in.store'), [
            'license_plate'   => 'B 2222 BB',
            'vehicle_type'    => 'motorcycle',
            'parking_area_id' => $inactiveArea->id,
        ]);

        $response->assertSessionHas('error');
    }
}
