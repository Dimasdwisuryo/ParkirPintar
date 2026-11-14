<?php

namespace Database\Factories;

use App\Models\ParkingArea;
use App\Models\ParkingTransaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * ParkingTransactionFactory
 *
 * Digunakan oleh PHPUnit feature tests.
 */
class ParkingTransactionFactory extends Factory
{
    protected $model = ParkingTransaction::class;

    public function definition(): array
    {
        $checkIn = Carbon::now()->subMinutes($this->faker->numberBetween(30, 300));

        return [
            'ticket_number'   => 'PKR-' . now()->format('Ymd') . '-' . strtoupper(Str::random(5)),
            'license_plate'   => $this->randomLicensePlate(),
            'vehicle_type'    => $this->faker->randomElement(['motorcycle', 'car']),
            'parking_area_id' => ParkingArea::factory(),
            'check_in_at'     => $checkIn,
            'check_out_at'    => null,
            'duration_minutes' => null,
            'fee'             => null,
            'status'          => 'active',
        ];
    }

    /**
     * Transaksi yang sudah selesai (completed).
     */
    public function completed(): static
    {
        return $this->state(function (array $attributes) {
            $checkIn  = Carbon::parse($attributes['check_in_at']);
            $checkOut = $checkIn->copy()->addMinutes($this->faker->numberBetween(20, 480));

            return [
                'check_out_at'     => $checkOut,
                'duration_minutes' => (int) $checkIn->diffInMinutes($checkOut),
                'fee'              => $this->faker->numberBetween(0, 50_000),
                'status'           => 'completed',
            ];
        });
    }

    /**
     * Generate random Indonesian-style license plate.
     */
    private function randomLicensePlate(): string
    {
        $prefixes = ['B', 'D', 'F', 'T', 'Z', 'AB', 'AD', 'AG'];
        $prefix   = $this->faker->randomElement($prefixes);
        $number   = $this->faker->numberBetween(1, 9999);
        $suffix   = strtoupper($this->faker->lexify('???'));

        return "{$prefix} {$number} {$suffix}";
    }
}
