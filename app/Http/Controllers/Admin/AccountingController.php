<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Account;
use App\Models\Transaction;
use App\Models\Ledger;
use Illuminate\Http\Request;

class AccountingController extends Controller
{
    public function index()
    {
        // Assets
        $assets = Account::where('type', 'Asset')->get();
        $totalAssets = $assets->sum('balance');

        // Liabilities
        $liabilities = Account::where('type', 'Liability')->get();
        $totalLiabilities = $liabilities->sum('balance');

        // Equity
        $equity = Account::where('type', 'Equity')->get();
        $totalEquity = $equity->sum('balance');

        // Revenue (Credits - Debits)
        $revenueAccounts = Account::where('type', 'Revenue')->get();
        $totalRevenue = $revenueAccounts->sum('balance');

        // Expenses (Debits - Credits)
        $expenseAccounts = Account::where('type', 'Expense')->get();
        $totalExpenses = $expenseAccounts->sum('balance');

        $netProfit = $totalRevenue - $totalExpenses;

        $recentTransactions = Transaction::with('ledgers.account')
            ->latest()
            ->limit(10)
            ->get();

        return view('admin.accounting.index', compact(
            'assets',
            'totalAssets',
            'liabilities',
            'totalLiabilities',
            'equity',
            'totalEquity',
            'totalRevenue',
            'totalExpenses',
            'netProfit',
            'recentTransactions'
        ));
    }

    public function vatReport()
    {
        $vatPayableAccount = Account::where('code', '2200')->first();
        $totalVatCollected = $vatPayableAccount ? $vatPayableAccount->balance : 0;

        if (config('database.default') === 'sqlite') {
            $vatByMonth = \App\Models\OrderItem::selectRaw('SUM(vat_amount) as total, strftime("%m", created_at) as month, strftime("%Y", created_at) as year')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();
        } else {
            $vatByMonth = \App\Models\OrderItem::selectRaw('SUM(vat_amount) as total, MONTH(created_at) as month, YEAR(created_at) as year')
                ->groupBy('year', 'month')
                ->orderBy('year', 'desc')
                ->orderBy('month', 'desc')
                ->get();
        }

        $recentVatTransfers = Ledger::with('transaction')
            ->where('account_id', $vatPayableAccount->id ?? 0)
            ->latest()
            ->paginate(15);

        return view('admin.accounting.vat', compact('totalVatCollected', 'vatByMonth', 'recentVatTransfers'));
    }
}
