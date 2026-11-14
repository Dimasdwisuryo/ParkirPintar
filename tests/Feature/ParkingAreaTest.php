<?php

namespace Tests\Feature;

use App\Models\ParkingArea;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * ParkingAreaTest
 *
 * Feature test untuk Modul 1: Manajemen Area Parkir
 * FR-01 s/d FR-06
 */
class ParkingAreaTest extends TestCase
{
    use RefreshDatabase;

    // ─── FR-02: Daftar Area ───────────────────────────────────────────────────

    public function test_can_list_parking_areas(): void
    {
        ParkingArea::factory()->create(['name' => 'Area Tes']);

        $response = $this->get(route('parking-areas.index'));

        $response->assertStatus(200);
        $response->assertSee('Area Tes');
    }

    // ─── FR-01: Tambah Area ───────────────────────────────────────────────────

    public function test_can_create_parking_area(): void
    {
        $response = $this->post(route('parking-areas.store'), [
            'name'                => 'Parkir Baru',
            'location'            => 'Lantai 1',
            'capacity_motorcycle' => 20,
            'capacity_car'        => 10,
        ]);

        $response->assertRedirect(route('parking-areas.index'));
        $this->assertDatabaseHas('parking_areas', ['name' => 'Parkir Baru']);
    }

    // ─── FR-05: Validasi Kapasitas Negatif ────────────────────────────────────

    public function test_cannot_create_area_with_negative_capacity(): void
    {
        $response = $this->post(route('parking-areas.store'), [
            'name'                => 'Area Negatif',
            'capacity_motorcycle' => -1,
            'capacity_car'        => 0,
        ]);

        $response->assertSessionHasErrors('capacity_motorcycle');
    }

    // ─── FR-01: Nama unik ─────────────────────────────────────────────────────

    public function test_cannot_create_area_with_duplicate_name(): void
    {
        ParkingArea::factory()->create(['name' => 'Area Duplikat']);

        $response = $this->post(route('parking-areas.store'), [
            'name'                => 'Area Duplikat',
            'capacity_motorcycle' => 10,
            'capacity_car'        => 5,
        ]);

        $response->assertSessionHasErrors('name');
    }

    // ─── FR-03/FR-05: Update — kapasitas tidak boleh di bawah occupied ────────

    public function test_cannot_reduce_capacity_below_occupied(): void
    {
        $area = ParkingArea::factory()->create([
            'capacity_motorcycle' => 10,
            'capacity_car'        => 10,
        ]);

        // Simulasikan 3 motor terparkir
        \App\Models\ParkingTransaction::factory()->count(3)->create([
            'parking_area_id' => $area->id,
            'vehicle_type'    => 'motorcycle',
            'status'          => 'active',
        ]);

        $response = $this->put(route('parking-areas.update', $area), [
            'name'                => $area->name,
            'capacity_motorcycle' => 2, // di bawah 3 yang terparkir
            'capacity_car'        => 10,
        ]);

        $response->assertSessionHasErrors('capacity_motorcycle');
    }

    // ─── FR-04: Hapus area tanpa transaksi ───────────────────────────────────

    public function test_can_delete_area_with_no_transactions(): void
    {
        $area = ParkingArea::factory()->create();

        $response = $this->delete(route('parking-areas.destroy', $area));

        $response->assertRedirect(route('parking-areas.index'));
        $this->assertDatabaseMissing('parking_areas', ['id' => $area->id]);
    }

    // ─── FR-04: Area dengan riwayat transaksi dinonaktifkan ──────────────────

    public function test_area_with_history_is_deactivated_not_deleted(): void
    {
        $area = ParkingArea::factory()->create(['is_active' => true]);

        // Buat transaksi yang sudah selesai (riwayat)
        \App\Models\ParkingTransaction::factory()->create([
            'parking_area_id' => $area->id,
            'status'          => 'completed',
        ]);

        $response = $this->delete(route('parking-areas.destroy', $area));

        $response->assertRedirect(route('parking-areas.index'));
        // Harus tetap ada di DB tapi dinonaktifkan
        $this->assertDatabaseHas('parking_areas', ['id' => $area->id, 'is_active' => false]);
    }

    // ─── FR-04: Tidak bisa hapus area yang masih ada transaksi aktif ─────────

    public function test_cannot_delete_area_with_active_transactions(): void
    {
        $area = ParkingArea::factory()->create();

        \App\Models\ParkingTransaction::factory()->create([
            'parking_area_id' => $area->id,
            'status'          => 'active',
        ]);

        $response = $this->delete(route('parking-areas.destroy', $area));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('parking_areas', ['id' => $area->id]);
    }
}
