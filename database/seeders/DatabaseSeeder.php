<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * NFR-11: Database dikelola melalui migration dan dilengkapi seeder.
     */
    public function run(): void
    {
        // Data area parkir untuk development & demo
        $this->call(ParkingAreaSeeder::class);

        // User default untuk development
        User::factory()->create([
            'name'  => 'Admin ParkirPintar',
            'email' => 'admin@parkirpintar.local',
        ]);
    }
}
