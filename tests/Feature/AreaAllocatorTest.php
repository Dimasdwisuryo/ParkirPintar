<?php

namespace Tests\Feature;

use App\Models\ParkingArea;
use App\Models\ParkingTransaction;
use App\Services\ParkingAllocationService;
use App\Services\ParkingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AreaAllocatorTest extends TestCase
{
    use RefreshDatabase;

    public function test_sequential_fill_allocation_until_full(): void
    {
        $area1 = ParkingArea::factory()->create([
            'name'         => 'Basement 1',
            'capacity_car' => 2,
            'is_active'    => true,
        ]);

        $area2 = ParkingArea::factory()->create([
            'name'         => 'Basement 2',
            'capacity_car' => 2,
            'is_active'    => true,
        ]);

        $parkingService = app(ParkingService::class);

        // Check in mobil pertama -> Area 1
        $t1 = $parkingService->checkIn('B 1111 AA', 'car');
        $this->assertEquals($area1->id, $t1->parking_area_id);

        // Check in mobil kedua -> Area 1 (Area 1 terisi penuh 2/2)
        $t2 = $parkingService->checkIn('B 2222 BB', 'car');
        $this->assertEquals($area1->id, $t2->parking_area_id);

        // Check in mobil ketiga -> harus otomatis dialihkan ke Area 2
        $t3 = $parkingService->checkIn('B 3333 CC', 'car');
        $this->assertEquals($area2->id, $t3->parking_area_id);

        // Check in mobil keempat -> Area 2 (Area 2 terisi penuh 2/2)
        $t4 = $parkingService->checkIn('B 4444 DD', 'car');
        $this->assertEquals($area2->id, $t4->parking_area_id);

        // Check in mobil kelima -> semua area penuh, harus ditolak
        $this->expectException(\RuntimeException::class);
        $this->expectExceptionMessage('Kapasitas parkir untuk jenis kendaraan mobil sudah penuh.');
        $parkingService->checkIn('B 5555 EE', 'car');
    }
}
