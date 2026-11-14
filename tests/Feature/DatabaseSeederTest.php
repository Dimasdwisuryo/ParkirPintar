<?php

namespace Tests\Feature;

use App\Models\ParkingArea;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * DatabaseSeederTest
 *
 * Memverifikasi bahwa DatabaseSeeder dan ParkingAreaSeeder berjalan sempurna (NFR-11).
 */
class DatabaseSeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_database_seeder_populates_data_successfully(): void
    {
        $this->seed(DatabaseSeeder::class);

        // Verifikasi area parkir dibuat
        $this->assertGreaterThan(0, ParkingArea::count());
        $this->assertDatabaseHas('parking_areas', [
            'name' => 'Parkir Gedung A',
        ]);

        // Verifikasi user default dibuat
        $this->assertDatabaseHas('users', [
            'email' => 'admin@parkirpintar.local',
        ]);
    }
}
