<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\PurchaseOrderItem>
 */
class PurchaseOrderItemFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $product = \App\Models\Product::factory()->create();
        return [
            'purchase_order_id' => \App\Models\PurchaseOrder::factory(),
            'product_id' => $product->id,
            'quantity' => $this->faker->numberBetween(10, 100),
            'unit_cost' => $product->cost_price,
            'subtotal' => 0, // Should be calculated
        ];
    }
}
