<?php

namespace Database\Seeders;

use App\Models\ParkingArea;
use Illuminate\Database\Seeder;

/**
 * ParkingAreaSeeder
 *
 * NFR-11: Database dikelola melalui migration dan dilengkapi seeder.
 * Menyediakan data area parkir sample untuk development dan demo.
 */
class ParkingAreaSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            [
                'name'                => 'Parkir Gedung A',
                'location'            => 'Lantai Dasar Gedung A, Pintu Utara',
                'capacity_motorcycle' => 50,
                'capacity_car'        => 20,
                'is_active'           => true,
            ],
            [
                'name'                => 'Parkir Gedung B',
                'location'            => 'Basement Gedung B',
                'capacity_motorcycle' => 30,
                'capacity_car'        => 15,
                'is_active'           => true,
            ],
            [
                'name'                => 'Parkir Outdoor Barat',
                'location'            => 'Area Parkir Terbuka, Sisi Barat',
                'capacity_motorcycle' => 100,
                'capacity_car'        => 40,
                'is_active'           => true,
            ],
            [
                'name'                => 'Parkir VIP',
                'location'            => 'Lantai P1, Khusus Tamu VIP',
                'capacity_motorcycle' => 10,
                'capacity_car'        => 10,
                'is_active'           => true,
            ],
            [
                'name'                => 'Parkir Kantin',
                'location'            => 'Dekat Kantin Utama',
                'capacity_motorcycle' => 25,
                'capacity_car'        => 5,
                'is_active'           => true,
            ],
        ];

        foreach ($areas as $area) {
            ParkingArea::firstOrCreate(
                ['name' => $area['name']],
                $area
            );
        }

        $this->command->info('✅ ParkingAreaSeeder: ' . count($areas) . ' area parkir berhasil di-seed.');
    }
}
