<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categories = Category::whereNotNull('parent_id')->get();
        $brands = Brand::all();

        if ($categories->isEmpty() || $brands->isEmpty()) {
            $this->command->info('Please run ContentSeeder first to create categories and brands.');
            return;
        }

        $productNames = [
            'Pro',
            'Elite',
            'Ultra',
            'Max',
            'Air',
            'Classic',
            'Modern',
            'Essential',
            'Premium',
            'Dynamic',
            'Sleek',
            'Compact',
            'Robust',
            'Powerful',
            'Smart',
            'Advanced',
            'Original',
            'Limited',
            'Special',
            'Ultimate'
        ];

        $productTypes = [
            'Wireless',
            'Bluetooth',
            'Digital',
            'Analog',
            'Portable',
            'Desktop',
            'Home',
            'Office',
            'Extreme',
            'Studio'
        ];

        for ($i = 1; $i <= 100; $i++) {
            $category = $categories->random();
            $brand = $brands->random();
            $name = $brand->name . ' ' . $productNames[array_rand($productNames)] . ' ' . $productTypes[array_rand($productTypes)] . ' ' . $i;
            $slug = Str::slug($name);
            $sku = strtoupper(substr($brand->name, 0, 3)) . '-' . strtoupper(Str::random(5)) . '-' . $i;

            Product::create([
                'name' => $name,
                'slug' => $slug,
                'sku' => $sku,
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'regular_price' => rand(50, 2000),
                'sale_price' => rand(0, 1) ? rand(40, 1800) : null,
                'stock_quantity' => rand(0, 200),
                'image_url' => 'https://picsum.photos/seed/' . $slug . '/800/600',
                'description' => 'This is a premium ' . strtolower($name) . ' designed for the best user experience.',
                'featured' => (bool) rand(0, 1),
            ]);
        }

        $this->command->info('100 products have been seeded successfully!');
    }
}
