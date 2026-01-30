<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => \App\Models\User::factory(),
            'order_number' => 'ORD-' . strtoupper($this->faker->unique()->bothify('??####')),
            'status' => $this->faker->randomElement(['pending', 'processing', 'completed', 'cancelled', 'declined']),
            'grand_total' => 0, // Should be calculated
            'item_count' => 0, // Should be calculated
            'is_paid' => $this->faker->boolean(80),
            'payment_method' => $this->faker->randomElement(['cash_on_delivery', 'card', 'paypal']),
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'address' => $this->faker->streetAddress(),
            'city' => $this->faker->city(),
            'zip_code' => $this->faker->postcode(),
        ];
    }
}
