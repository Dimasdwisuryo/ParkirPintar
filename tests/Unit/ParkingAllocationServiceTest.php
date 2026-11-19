<?php

namespace Tests\Unit;

use App\Models\ParkingArea;
use App\Models\ParkingTransaction;
use App\Services\ParkingAllocationService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ParkingAllocationServiceTest extends TestCase
{
    use RefreshDatabase;

    private ParkingAllocationService $allocationService;

    protected function setUp(): void
    {
        parent::setUp();
        $this->allocationService = new ParkingAllocationService();
    }

    public function test_it_allocates_sequentially_until_first_area_is_full(): void
    {
        // Buat Area 1 (kapasitas 2 mobil) dan Area 2 (kapasitas 3 mobil)
        $area1 = ParkingArea::factory()->create([
            'name'         => 'Area A',
            'capacity_car' => 2,
            'is_active'    => true,
        ]);

        $area2 = ParkingArea::factory()->create([
            'name'         => 'Area B',
            'capacity_car' => 3,
            'is_active'    => true,
        ]);

        // Mobil 1 dialokasikan ke Area 1
        $allocated = $this->allocationService->allocate('car');
        $this->assertEquals($area1->id, $allocated->id);

        ParkingTransaction::factory()->create([
            'parking_area_id' => $area1->id,
            'vehicle_type'    => 'car',
            'status'          => 'active',
        ]);

        // Mobil 2 tetap dialokasikan ke Area 1 (karena belum penuh, 1/2)
        $allocated = $this->allocationService->allocate('car');
        $this->assertEquals($area1->id, $allocated->id);

        ParkingTransaction::factory()->create([
            'parking_area_id' => $area1->id,
            'vehicle_type'    => 'car',
            'status'          => 'active',
        ]);

        // Sekarang Area 1 terisi penuh (2/2)
        // Mobil 3 harus otomatis dialokasikan ke Area 2 (Sequential Fill)
        $allocated = $this->allocationService->allocate('car');
        $this->assertEquals($area2->id, $allocated->id);
    }

    public function test_it_returns_null_when_all_active_areas_are_full(): void
    {
        $area = ParkingArea::factory()->create([
            'capacity_motorcycle' => 1,
            'is_active'           => true,
        ]);

        // Isi slot motor
        ParkingTransaction::factory()->create([
            'parking_area_id' => $area->id,
            'vehicle_type'    => 'motorcycle',
            'status'          => 'active',
        ]);

        // Tidak ada lagi slot motor yang tersedia
        $allocated = $this->allocationService->allocate('motorcycle');
        $this->assertNull($allocated);
    }

    public function test_it_ignores_inactive_areas_even_if_empty(): void
    {
        // Area 1 tidak aktif
        $inactiveArea = ParkingArea::factory()->create([
            'name'         => 'Area Nonaktif',
            'capacity_car' => 10,
            'is_active'    => false,
        ]);

        // Area 2 aktif
        $activeArea = ParkingArea::factory()->create([
            'name'         => 'Area Aktif',
            'capacity_car' => 5,
            'is_active'    => true,
        ]);

        $allocated = $this->allocationService->allocate('car');
        $this->assertEquals($activeArea->id, $allocated->id);
    }
}
