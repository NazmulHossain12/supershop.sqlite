<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Product;
use Illuminate\Support\Str;

class ContentSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Brands
        $brands = [
            'Sony' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/c/ca/Sony_logo.svg/2000px-Sony_logo.svg.png',
            'Sennheiser' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/82/Sennheiser_logo.svg/2560px-Sennheiser_logo.svg.png',
            'Canon' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8e/Canon_logo.svg/2000px-Canon_logo.svg.png',
            'Nike' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/a/a6/Logo_NIKE.svg/1200px-Logo_NIKE.svg.png',
            'Apple' => 'https://upload.wikimedia.org/wikipedia/commons/thumb/f/fa/Apple_logo_black.svg/1667px-Apple_logo_black.svg.png',
        ];

        foreach ($brands as $name => $logo) {
            Brand::firstOrCreate(
                ['name' => $name],
                ['slug' => Str::slug($name), 'logo' => $logo]
            );
        }

        // 2. Create Categories
        $categories = [
            'Electronics' => ['Headphones', 'Cameras', 'Smartphones', 'Laptops'],
            'Fashion' => ['Men', 'Women', 'Accessories', 'Shoes'],
            'Home & Living' => ['Furniture', 'Decor', 'Kitchen'],
        ];

        foreach ($categories as $parentName => $subCategories) {
            $parent = Category::firstOrCreate(
                ['name' => $parentName],
                ['slug' => Str::slug($parentName)]
            );

            foreach ($subCategories as $subName) {
                Category::firstOrCreate(
                    ['name' => $subName, 'parent_id' => $parent->id],
                    ['slug' => Str::slug($subName)]
                );
            }
        }

        // 3. Create Products
        // Headphones (Electronics -> Headphones)
        $headphonesCat = Category::where('name', 'Headphones')->first();
        $sonyBrand = Brand::where('name', 'Sony')->first();

        Product::create([
            'name' => 'Sony WH-1000XM5',
            'slug' => 'sony-wh-1000xm5',
            'sku' => 'SNY-XM5-BLK',
            'category_id' => $headphonesCat->id,
            'brand_id' => $sonyBrand->id,
            'regular_price' => 399.00,
            'sale_price' => 348.00,
            'stock_quantity' => 50,
            'image_url' => 'https://images.unsplash.com/photo-1618366712010-f4ae9c647dcb?w=800&auto=format&fit=crop&q=60',
            'description' => 'Industry-leading noise cancellation.',
            'featured' => true,
        ]);

        // Camera (Electronics -> Cameras)
        $camerasCat = Category::where('name', 'Cameras')->first();
        $canonBrand = Brand::where('name', 'Canon')->first();

        Product::create([
            'name' => 'Canon EOS R6',
            'slug' => 'canon-eos-r6',
            'sku' => 'CAN-R6-BODY',
            'category_id' => $camerasCat->id,
            'brand_id' => $canonBrand->id,
            'regular_price' => 2499.00,
            'stock_quantity' => 10,
            'image_url' => 'https://images.unsplash.com/photo-1516035069371-29a1b244cc32?w=800&auto=format&fit=crop&q=60',
            'description' => 'Full-frame mirrorless camera.',
            'featured' => true,
        ]);

        // Shoes (Fashion -> Shoes)
        $shoesCat = Category::where('name', 'Shoes')->first();
        $nikeBrand = Brand::where('name', 'Nike')->first();

        Product::create([
            'name' => 'Nike Air Max 90',
            'slug' => 'nike-air-max-90',
            'sku' => 'NKE-AM90-WHT',
            'category_id' => $shoesCat->id,
            'brand_id' => $nikeBrand->id,
            'regular_price' => 130.00,
            'stock_quantity' => 100,
            'image_url' => 'https://images.unsplash.com/photo-1542291026-7eec264c27ff?w=800&auto=format&fit=crop&q=60',
            'description' => 'Classic comfort and style.',
            'featured' => false,
        ]);
    }
}
