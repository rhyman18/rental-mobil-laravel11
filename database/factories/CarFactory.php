<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Car>
 */
class CarFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'brand' => $this->faker->company,       // Random car brand
            'model' => $this->faker->word,          // Random car model
            'license_plate' => strtoupper($this->faker->bothify('?? ### ??')), // Random license plate
            'daily_rate' => $this->faker->numberBetween(30000, 120000), // Random rate between 100,000 and 500,000
            // 'available' => $this->faker->boolean,   // Randomly true (1) or false (0)
            'available' => 1
        ];
    }
}
