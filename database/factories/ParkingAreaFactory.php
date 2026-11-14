<?php

namespace Database\Factories;

use App\Models\ParkingArea;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * ParkingAreaFactory
 *
 * Digunakan oleh PHPUnit feature tests.
 */
class ParkingAreaFactory extends Factory
{
    protected $model = ParkingArea::class;

    public function definition(): array
    {
        return [
            'name'                => $this->faker->unique()->words(3, true),
            'location'            => $this->faker->address(),
            'capacity_motorcycle' => $this->faker->numberBetween(10, 100),
            'capacity_car'        => $this->faker->numberBetween(5, 50),
            'is_active'           => true,
        ];
    }

    /**
     * Area yang tidak aktif.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }
}
