<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Supplier;

class SupplierSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $suppliers = [
            [
                'name' => 'Global Electronics Ltd',
                'contact_person' => 'John Doe',
                'email' => 'sales@globalelectronics.com',
                'phone' => '1234567890',
                'address' => '123 Silicon Valley, CA',
                'tax_number' => 'TAX-12345',
                'opening_balance' => 0,
                'current_balance' => 0,
            ],
            [
                'name' => 'Fashion Hub Wholesalers',
                'contact_person' => 'Jane Smith',
                'email' => 'jane@fashionhub.com',
                'phone' => '0987654321',
                'address' => '456 Fashion Ave, NY',
                'tax_number' => 'TAX-67890',
                'opening_balance' => 0,
                'current_balance' => 0,
            ],
            [
                'name' => 'Premium Groceries Co',
                'contact_person' => 'Bob Builder',
                'email' => 'bob@premiumgroceries.com',
                'phone' => '1122334455',
                'address' => '789 Farm Rd, TX',
                'tax_number' => 'TAX-11223',
                'opening_balance' => 0,
                'current_balance' => 0,
            ],
        ];

        foreach ($suppliers as $supplier) {
            Supplier::create($supplier);
        }
    }
}
