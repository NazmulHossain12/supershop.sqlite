<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');

        // Ledger - All Transactions
        $transactions = Transaction::whereBetween('transaction_date', [$startDate, $endDate])
            ->orderBy('transaction_date', 'desc')
            ->paginate(20);

        // Financial Summary
        $totalSales = Transaction::where('type', 'sale')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $totalRefunds = Transaction::where('type', 'refund')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $totalExpenses = Transaction::where('type', 'expense')
            ->whereBetween('transaction_date', [$startDate, $endDate])
            ->sum('amount');

        $netProfit = $totalSales - $totalRefunds - $totalExpenses;

        return view('admin.reports.index', compact(
            'transactions',
            'startDate',
            'endDate',
            'totalSales',
            'totalRefunds',
            'totalExpenses',
            'netProfit'
        ));
    }
}
