<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Roles & Permissions
        $this->call(RoleSeeder::class);

        // 2. Core Users
        $admin = User::factory()->create([
            'name' => 'Super Admin',
            'email' => 'admin@supershop.com',
            'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
        ]);
        $admin->assignRole('Super Admin');

        $cashiers = User::factory(2)->create();
        foreach ($cashiers as $cashier) {
            $cashier->assignRole('Cashier');
        }

        $customers = User::factory(5)->create();
        foreach ($customers as $customer) {
            $customer->assignRole('Customer');
        }

        // 3. Categories & Brands
        $categories = collect(['Electronics', 'Groceries', 'Clothing'])->map(function ($name) {
            return \App\Models\Category::factory()->create([
                'name' => $name,
                'slug' => \Illuminate\Support\Str::slug($name),
            ]);
        });

        $brands = \App\Models\Brand::factory(5)->create();
        $suppliers = \App\Models\Supplier::factory(3)->create();

        // 4. Products (10 total)
        $products = \App\Models\Product::factory(10)->recycle($categories)->recycle($brands)->recycle($suppliers)->create();

        // 5. Inventory: Stock Adjustments (5 total)
        \App\Models\StockAdjustment::factory(5)->recycle($products)->recycle($admin)->create();

        // 6. Procurement: 3 Purchase Orders
        $purchaseOrders = \App\Models\PurchaseOrder::factory(3)->recycle($suppliers)->create();
        foreach ($purchaseOrders as $po) {
            $items = \App\Models\PurchaseOrderItem::factory(3)->create([
                'purchase_order_id' => $po->id,
                'product_id' => $products->random()->id,
            ]);

            $total = $items->sum('subtotal');
            $po->update(['total_amount' => $total, 'paid_amount' => $total]);
        }

        // 7. Sales: 5 Invoices with 3 items each
        foreach ($customers as $customer) {
            $order = \App\Models\Order::factory()->create([
                'user_id' => $customer->id,
                'status' => 'completed',
                'is_paid' => true,
            ]);

            $orderItems = \App\Models\OrderItem::factory(3)->create([
                'order_id' => $order->id,
            ]);

            $total = $orderItems->sum(fn($item) => $item->price * $item->quantity);
            $order->update([
                'grand_total' => $total,
                'item_count' => $orderItems->sum('quantity')
            ]);

            $invoice = \App\Models\Invoice::factory()->create([
                'order_id' => $order->id,
                'total_amount' => $total,
            ]);

            foreach ($orderItems as $item) {
                \App\Models\InvoiceItem::factory()->create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'unit_price' => $item->price,
                    'subtotal' => $item->price * $item->quantity,
                ]);
            }
        }
    }
}
