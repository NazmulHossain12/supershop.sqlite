<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Account;

class ChartOfAccountsSeeder extends Seeder
{
    public function run(): void
    {
        $accounts = [
            // Assets
            ['name' => 'Cash on Hand', 'code' => '1001', 'type' => 'Asset'],
            ['name' => 'Bank Account', 'code' => '1002', 'type' => 'Asset'],
            ['name' => 'Accounts Receivable', 'code' => '1100', 'type' => 'Asset'],
            ['name' => 'Inventory', 'code' => '1200', 'type' => 'Asset'],

            // Liabilities
            ['name' => 'Accounts Payable', 'code' => '2100', 'type' => 'Liability'],
            ['name' => 'Sales Tax Payable', 'code' => '2200', 'type' => 'Liability'],

            // Equity
            ['name' => 'Owner Equity', 'code' => '3000', 'type' => 'Equity'],
            ['name' => 'Retained Earnings', 'code' => '3100', 'type' => 'Equity'],

            // Revenue
            ['name' => 'Sales Revenue', 'code' => '4001', 'type' => 'Revenue'],
            ['name' => 'Shipping Revenue', 'code' => '4100', 'type' => 'Revenue'],

            // Expenses
            ['name' => 'Cost of Goods Sold', 'code' => '5001', 'type' => 'Expense'],
            ['name' => 'Marketing Expense', 'code' => '5100', 'type' => 'Expense'],
            ['name' => 'Shipping Expense', 'code' => '5200', 'type' => 'Expense'],
            ['name' => 'Rent Expense', 'code' => '5300', 'type' => 'Expense'],
            ['name' => 'Utilities Expense', 'code' => '5400', 'type' => 'Expense'],
        ];

        foreach ($accounts as $account) {
            Account::firstOrCreate(['code' => $account['code']], $account);
        }
    }
}
