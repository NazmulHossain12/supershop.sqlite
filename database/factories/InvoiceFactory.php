<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Invoice>
 */
class InvoiceFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => \App\Models\Order::factory(),
            'invoice_number' => 'INV-' . strtoupper($this->faker->unique()->bothify('??####')),
            'total_amount' => 0, // Should be calculated
            'issued_at' => now(),
        ];
    }
}
