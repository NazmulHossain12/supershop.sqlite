<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Product;
use App\Models\Category;
use App\Models\Brand;
use App\Models\Supplier;
use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Invoice;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class MissionSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Users & Roles
        // Super Admin
        $admin = User::firstOrCreate(
            ['email' => 'admin@supershop.com'],
            [
                'name' => 'Super Admin',
                'password' => Hash::make('admin123'),
                'phone' => '1234567890',
            ]
        );
        $admin->assignRole('Super Admin');

        // Cashier
        $cashier = User::firstOrCreate(
            ['email' => 'cashier@supershop.com'],
            [
                'name' => 'Cashier Joe',
                'password' => Hash::make('cashier123'),
                'phone' => '0987654321',
            ]
        );
        $cashier->assignRole('Cashier');

        // 3 Customers
        $customers = [];
        for ($i = 1; $i <= 3; $i++) {
            $customers[] = User::create([
                'name' => "Customer $i",
                'email' => "customer$i@example.com",
                'password' => Hash::make('password'),
                'phone' => "999000000$i",
                'loyalty_points_balance' => 500,
            ]);
        }

        // 2. Inventory Prerequisites
        $category = Category::firstOrCreate(['name' => 'Electronics'], ['slug' => 'electronics']);
        $brand = Brand::firstOrCreate(['name' => 'SuperBrand'], ['slug' => 'superbrand']);

        // 5 Suppliers
        $suppliers = [];
        for ($i = 1; $i <= 5; $i++) {
            $suppliers[] = Supplier::create([
                'name' => "Global Supplier $i",
                'contact_person' => "Contact $i",
                'email' => "supplier$i@global.com",
                'phone' => "111222333$i",
                'address' => "Supplier Address $i",
            ]);
        }

        // 3. Inventory: 5 Products
        $products = [];
        for ($i = 1; $i <= 5; $i++) {
            $isFeatured = $i <= 3 ? 1 : 0;
            $products[] = Product::create([
                'name' => "Mission Product $i",
                'slug' => "mission-product-$i",
                'sku' => "MIS-PRD-00$i",
                'barcode' => "100000000000$i",
                'regular_price' => 100 * $i,
                'cost_price' => 70 * $i,
                'stock_quantity' => 50,
                'featured' => $isFeatured,
                'status' => 1,
                'category_id' => $category->id,
                'brand_id' => $brand->id,
                'supplier_id' => $suppliers[0]->id,
                'unit' => 'pcs',
                'description' => "Test product $i for mission data injection.",
            ]);
        }

        // 4. Transactions
        // 2 "Received" Purchase Orders
        for ($i = 1; $i <= 2; $i++) {
            $po = PurchaseOrder::create([
                'supplier_id' => $suppliers[0]->id,
                'reference_no' => "PO-MIS-00$i",
                'status' => 'Received',
                'total_amount' => 1000,
            ]);

            PurchaseOrderItem::create([
                'purchase_order_id' => $po->id,
                'product_id' => $products[0]->id,
                'quantity' => 10,
                'unit_cost' => 50,
                'subtotal' => 500,
            ]);
        }

        // 3 Sales Invoices (link to test customers)
        foreach ($customers as $index => $customer) {
            $order = Order::create([
                'user_id' => $customer->id,
                'order_number' => "ORD-MIS-00" . ($index + 1),
                'grand_total' => 200,
                'status' => 'completed',
                'is_paid' => true,
                'payment_method' => 'cash_on_delivery',
                'item_count' => 1,
                'first_name' => $customer->name,
                'last_name' => 'Test',
                'email' => $customer->email,
                'phone' => $customer->phone,
                'address' => '123 Test Street',
                'city' => 'Test City',
                'zip_code' => '12345',
            ]);

            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $products[0]->id,
                'quantity' => 1,
                'price' => 200,
            ]);

            Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => "INV-MIS-00" . ($index + 1),
                'total_amount' => 200,
                'issued_at' => now(),
            ]);
        }
    }
}
