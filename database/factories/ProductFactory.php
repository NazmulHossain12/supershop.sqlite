<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = $this->faker->unique()->sentence(3);
        $cost = $this->faker->randomFloat(2, 10, 500);
        return [
            'name' => $name,
            'slug' => \Illuminate\Support\Str::slug($name),
            'sku' => $this->faker->unique()->bothify('SKU-####-????'),
            'barcode' => $this->faker->unique()->ean13(),
            'description' => $this->faker->paragraph(),
            'short_description' => $this->faker->sentence(),
            'category_id' => \App\Models\Category::factory(),
            'brand_id' => \App\Models\Brand::factory(),
            'supplier_id' => \App\Models\Supplier::factory(),
            'cost_price' => $cost,
            'regular_price' => $cost * 1.5,
            'sale_price' => $this->faker->boolean(30) ? $cost * 1.3 : null,
            'stock_quantity' => $this->faker->numberBetween(0, 100),
            'unit' => 'pcs',
            'tax_applicable' => true,
            'vat_rate' => 15.00,
            'featured' => $this->faker->boolean(20),
            'status' => 1,
            'image_url' => 'https://via.placeholder.com/800x800.png?text=' . urlencode($name),
        ];
    }
}
