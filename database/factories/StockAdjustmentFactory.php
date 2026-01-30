<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\StockAdjustment>
 */
class StockAdjustmentFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => \App\Models\Product::factory(),
            'user_id' => \App\Models\User::factory(),
            'quantity' => $this->faker->numberBetween(-10, 50),
            'type' => $this->faker->randomElement(['addition', 'subtraction', 'damage', 'return']),
            'reason' => $this->faker->sentence(),
        ];
    }
}
