<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

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

        // User default untuk testing bawaan (DatabaseSeederTest)
        User::updateOrCreate(
            ['email' => 'admin@parkirpintar.local'],
            [
                'name'     => 'Admin ParkirPintar',
                'password' => Hash::make('password'),
                'role'     => 'admin',
            ]
        );

        // 2 Akun Penanggung Jawab Operasional ParkirPintar
        User::updateOrCreate(
            ['email' => 'windah@parkirpintar.id'],
            [
                'name'     => 'Windah Lamuri',
                'password' => Hash::make('12345678'),
                'role'     => 'admin',
            ]
        );

        User::updateOrCreate(
            ['email' => 'samuel@parkirpintar.id'],
            [
                'name'     => 'Samuel Shiva',
                'password' => Hash::make('12345678'),
                'role'     => 'admin',
            ]
        );
    }
}
