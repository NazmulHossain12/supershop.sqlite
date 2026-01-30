<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrder>
 */
class PurchaseOrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'supplier_id' => \App\Models\Supplier::factory(),
            'reference_no' => 'PO-' . strtoupper($this->faker->unique()->bothify('??####')),
            'status' => $this->faker->randomElement(['Draft', 'Ordered', 'Received', 'Cancelled']),
            'total_amount' => 0, // Should be calculated
            'paid_amount' => 0,
            'expected_delivery_date' => $this->faker->dateTimeBetween('now', '+1 month'),
        ];
    }
}
