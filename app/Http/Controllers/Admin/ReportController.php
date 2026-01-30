<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Models\Order;
use App\Models\PurchaseOrder;
use App\Models\Invoice;
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

    public function vatReport(Request $request)
    {
        $startDate = $request->start_date ?? now()->startOfMonth()->format('Y-m-d');
        $endDate = $request->end_date ?? now()->endOfMonth()->format('Y-m-d');

        // Output VAT (Sales Invoices & Orders)
        $outputVat = Invoice::whereBetween('issued_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('total_vat_amount');

        // Input VAT (Purchase Orders - Received)
        $inputVat = PurchaseOrder::where('status', 'Received')
            ->whereBetween('updated_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->sum('total_vat_amount');

        $netVat = $outputVat - $inputVat;

        // Details - Sales VAT Breakdown
        $salesDetails = Invoice::with('order')
            ->whereBetween('issued_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('issued_at', 'desc')
            ->get();

        // Details - Purchase VAT Breakdown
        $purchaseDetails = PurchaseOrder::with('supplier')
            ->where('status', 'Received')
            ->whereBetween('updated_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59'])
            ->orderBy('updated_at', 'desc')
            ->get();

        return view('admin.reports.vat', compact(
            'startDate',
            'endDate',
            'outputVat',
            'inputVat',
            'netVat',
            'salesDetails',
            'purchaseDetails'
        ));
    }

    public function inventory()
    {
        $products = \App\Models\Product::with('category')->get();

        $totalStockValueCost = $products->sum(function ($p) {
            return $p->stock_quantity * $p->cost_price;
        });

        $totalStockValueRetail = $products->sum(function ($p) {
            return $p->stock_quantity * ($p->sale_price ?? $p->regular_price);
        });

        return view('admin.reports.inventory', compact('products', 'totalStockValueCost', 'totalStockValueRetail'));
    }

    public function downloadPandL(Request $request)
    {
        $format = $request->query('format', 'pdf');
        $start = $request->query('start_date', now()->startOfMonth()->format('Y-m-d'));
        $end = $request->query('end_date', now()->endOfMonth()->format('Y-m-d'));

        if ($format === 'csv') {
            return $this->exportPandLCSV($start, $end);
        }
        return $this->exportPandLPDF($start, $end);
    }

    public function downloadInventory(Request $request)
    {
        $format = $request->input('format', 'pdf');
        if ($format === 'csv') {
            return $this->exportInventoryCSV();
        }
        return $this->exportInventoryPDF();
    }

    public function downloadVat(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $start = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        if ($format === 'csv') {
            return $this->exportVatCSV($start, $end);
        }
        return $this->exportVatPDF($start, $end);
    }

    public function balanceSheet()
    {
        $assets = \App\Models\Account::where('type', 'Asset')->get();
        $liabilities = \App\Models\Account::where('type', 'Liability')->get();
        $equity = \App\Models\Account::where('type', 'Equity')->get();

        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity = $equity->sum('balance');

        return view('admin.reports.balance-sheet', compact('assets', 'liabilities', 'equity', 'totalAssets', 'totalLiabilities', 'totalEquity'));
    }

    public function downloadBalanceSheet(Request $request)
    {
        $format = $request->input('format', 'pdf');
        if ($format === 'csv') {
            return $this->exportBalanceSheetCSV();
        }
        return $this->exportBalanceSheetPDF();
    }

    public function trialBalance()
    {
        $accounts = \App\Models\Account::all();
        $totalDebits = \App\Models\Ledger::sum('debit');
        $totalCredits = \App\Models\Ledger::sum('credit');

        return view('admin.reports.trial-balance', compact('accounts', 'totalDebits', 'totalCredits'));
    }

    public function downloadTrialBalance(Request $request)
    {
        $format = $request->input('format', 'pdf');
        if ($format === 'csv') {
            return $this->exportTrialBalanceCSV();
        }
        return $this->exportTrialBalancePDF();
    }

    public function cashflow(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        // Cash Inflows (Sales, Payments Received)
        $inflows = \App\Models\Ledger::whereHas('account', function ($q) {
            $q->where('name', 'like', '%Cash%')->orWhere('name', 'like', '%Bank%');
        })
            ->where('debit', '>', 0)
            ->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            })
            ->with(['transaction', 'account'])
            ->get();

        // Cash Outflows (Purchases, Expenses)
        $outflows = \App\Models\Ledger::whereHas('account', function ($q) {
            $q->where('name', 'like', '%Cash%')->orWhere('name', 'like', '%Bank%');
        })
            ->where('credit', '>', 0)
            ->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            })
            ->with(['transaction', 'account'])
            ->get();

        $totalInflow = $inflows->sum('debit');
        $totalOutflow = $outflows->sum('credit');
        $netCashflow = $totalInflow - $totalOutflow;

        return view('admin.reports.cashflow', compact('inflows', 'outflows', 'totalInflow', 'totalOutflow', 'netCashflow', 'startDate', 'endDate'));
    }

    public function downloadCashflow(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $start = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        if ($format === 'csv') {
            return $this->exportCashflowCSV($start, $end);
        }
        return $this->exportCashflowPDF($start, $end);
    }

    public function ledger(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $accountId = $request->input('account_id');

        $query = \App\Models\Ledger::with(['account', 'transaction'])
            ->whereHas('transaction', function ($q) use ($startDate, $endDate) {
                $q->whereBetween('transaction_date', [$startDate, $endDate]);
            });

        if ($accountId) {
            $query->where('account_id', $accountId);
        }

        $ledgers = $query->latest()->get();
        $accounts = \App\Models\Account::all();

        return view('admin.reports.ledger', compact('ledgers', 'accounts', 'startDate', 'endDate', 'accountId'));
    }

    public function downloadLedger(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $start = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));
        $accountId = $request->input('account_id');

        if ($format === 'csv') {
            return $this->exportLedgerCSV($start, $end, $accountId);
        }
        return $this->exportLedgerPDF($start, $end, $accountId);
    }

    public function invoices(Request $request)
    {
        $startDate = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $endDate = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        $query = \App\Models\Invoice::with(['customer', 'items.product'])
            ->whereBetween('issued_at', [$startDate . ' 00:00:00', $endDate . ' 23:59:59']);

        $invoices = $query->latest()->get();

        $totalSales = $invoices->sum('total_amount');
        $totalVat = $invoices->sum('total_vat_amount');

        return view('admin.reports.invoices', compact('invoices', 'startDate', 'endDate', 'totalSales', 'totalVat'));
    }

    public function downloadInvoices(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $start = $request->input('start_date', now()->startOfMonth()->format('Y-m-d'));
        $end = $request->input('end_date', now()->endOfMonth()->format('Y-m-d'));

        if ($format === 'csv') {
            return $this->exportInvoicesCSV($start, $end);
        }
        return $this->exportInvoicesPDF($start, $end);
    }

    // --- Export Helpers ---

    protected function exportPandLCSV($start, $end)
    {
        $callback = function () use ($start, $end) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Profit & Loss Report', $start . ' to ' . $end]);
            fputcsv($file, []);

            $sales = \App\Models\Transaction::where('type', 'sale')->whereBetween('transaction_date', [$start, $end])->sum('amount');
            $refunds = \App\Models\Transaction::where('type', 'refund')->whereBetween('transaction_date', [$start, $end])->sum('amount');
            $expenses = \App\Models\Transaction::where('type', 'expense')->whereBetween('transaction_date', [$start, $end])->sum('amount');

            fputcsv($file, ['Category', 'Amount']);
            fputcsv($file, ['Total Sales', number_format($sales, 2)]);
            fputcsv($file, ['Total Refunds', number_format($refunds, 2)]);
            fputcsv($file, ['Total Expenses', number_format($expenses, 2)]);
            fputcsv($file, ['Net Profit', number_format($sales - $refunds - $expenses, 2)]);

            fclose($file);
        };

        return response()->streamDownload($callback, 'p-and-l-' . $start . '-' . $end . '.csv');
    }

    protected function exportPandLPDF($start, $end)
    {
        $data = $this->getPandLData($start, $end);
        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('admin.reports.pdf.p-and-l', $data);
        return $pdf->download('p-and-l-' . $start . '-' . $end . '.pdf');
    }

    protected function exportVatCSV($start, $end)
    {
        $callback = function () use ($start, $end) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['VAT Report', $start . ' to ' . $end]);
            fputcsv($file, []);

            fputcsv($file, ['Type', 'Ref #', 'Date', 'VAT Amount']);

            $sales = \App\Models\Invoice::whereBetween('issued_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->get();
            foreach ($sales as $inv) {
                fputcsv($file, ['Output (Sale)', $inv->invoice_number, $inv->issued_at->format('Y-m-d'), $inv->total_vat_amount]);
            }

            $purchases = \App\Models\PurchaseOrder::where('status', 'Received')->whereBetween('updated_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->get();
            foreach ($purchases as $po) {
                fputcsv($file, ['Input (Purchase)', $po->reference_no, $po->updated_at->format('Y-m-d'), $po->total_vat_amount]);
            }

            fclose($file);
        };

        return response()->streamDownload($callback, 'vat-report-' . $start . '-' . $end . '.csv');
    }

    protected function exportVatPDF($start, $end)
    {
        $data = $this->getVatData($start, $end);
        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('admin.reports.pdf.vat', $data);
        return $pdf->download('vat-report-' . $start . '-' . $end . '.pdf');
    }

    protected function exportInventoryCSV()
    {
        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Inventory Valuation Report', date('Y-m-d')]);
            fputcsv($file, []);
            fputcsv($file, ['Product', 'SKU', 'Stock Qty', 'Avg Cost', 'Retail Price', 'Total Cost Value']);

            $products = \App\Models\Product::all();
            foreach ($products as $p) {
                fputcsv($file, [
                    $p->name,
                    $p->sku,
                    $p->stock_quantity,
                    $p->cost_price,
                    $p->sale_price ?? $p->regular_price,
                    $p->stock_quantity * $p->cost_price
                ]);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, 'inventory-valuation-' . date('Y-m-d') . '.csv');
    }

    protected function exportInventoryPDF()
    {
        $products = \App\Models\Product::with('category')->get();
        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('admin.reports.pdf.inventory', compact('products'));
        return $pdf->download('inventory-valuation-' . date('Y-m-d') . '.pdf');
    }

    private function getPandLData($start, $end)
    {
        $totalSales = \App\Models\Transaction::where('type', 'sale')->whereBetween('transaction_date', [$start, $end])->sum('amount');
        $totalRefunds = \App\Models\Transaction::where('type', 'refund')->whereBetween('transaction_date', [$start, $end])->sum('amount');
        $totalExpenses = \App\Models\Transaction::where('type', 'expense')->whereBetween('transaction_date', [$start, $end])->sum('amount');
        $netProfit = $totalSales - $totalRefunds - $totalExpenses;

        return compact('start', 'end', 'totalSales', 'totalRefunds', 'totalExpenses', 'netProfit');
    }

    protected function exportBalanceSheetCSV()
    {
        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Balance Sheet', date('Y-m-d')]);
            fputcsv($file, []);

            fputcsv($file, ['Category', 'Account', 'Balance']);

            $types = ['Asset', 'Liability', 'Equity'];
            foreach ($types as $type) {
                fputcsv($file, [$type]);
                $accounts = \App\Models\Account::where('type', $type)->get();
                foreach ($accounts as $acc) {
                    fputcsv($file, ['', $acc->name, $acc->balance]);
                }
                fputcsv($file, ['', 'Total ' . $type, $accounts->sum('balance')]);
                fputcsv($file, []);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, 'balance-sheet-' . date('Y-m-d') . '.csv');
    }

    protected function exportBalanceSheetPDF()
    {
        $assets = \App\Models\Account::where('type', 'Asset')->get();
        $liabilities = \App\Models\Account::where('type', 'Liability')->get();
        $equity = \App\Models\Account::where('type', 'Equity')->get();
        $totalAssets = $assets->sum('balance');
        $totalLiabilities = $liabilities->sum('balance');
        $totalEquity = $equity->sum('balance');

        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('admin.reports.pdf.balance-sheet', compact('assets', 'liabilities', 'equity', 'totalAssets', 'totalLiabilities', 'totalEquity'));
        return $pdf->download('balance-sheet-' . date('Y-m-d') . '.pdf');
    }

    protected function exportTrialBalanceCSV()
    {
        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Trial Balance', date('Y-m-d')]);
            fputcsv($file, []);
            fputcsv($file, ['Account', 'Code', 'Debit', 'Credit']);

            $accounts = \App\Models\Account::all();
            foreach ($accounts as $acc) {
                $d = $acc->ledgers()->sum('debit');
                $c = $acc->ledgers()->sum('credit');
                if ($d > 0 || $c > 0) {
                    fputcsv($file, [$acc->name, $acc->code, $d, $c]);
                }
            }
            fputcsv($file, ['Total', '', \App\Models\Ledger::sum('debit'), \App\Models\Ledger::sum('credit')]);
            fclose($file);
        };

        return response()->streamDownload($callback, 'trial-balance-' . date('Y-m-d') . '.csv');
    }

    protected function exportTrialBalancePDF()
    {
        $accounts = \App\Models\Account::all();
        $totalDebits = \App\Models\Ledger::sum('debit');
        $totalCredits = \App\Models\Ledger::sum('credit');
        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('admin.reports.pdf.trial-balance', compact('accounts', 'totalDebits', 'totalCredits'));
        return $pdf->download('trial-balance-' . date('Y-m-d') . '.pdf');
    }

    protected function exportCashflowCSV($start, $end)
    {
        $callback = function () use ($start, $end) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Cashflow Statement', $start . ' to ' . $end]);
            fputcsv($file, []);

            fputcsv($file, ['Inflows']);
            fputcsv($file, ['Date', 'Account', 'Amount']);
            $inflows = \App\Models\Ledger::whereHas('account', fn($q) => $q->where('name', 'like', '%Cash%')->orWhere('name', 'like', '%Bank%'))
                ->where('debit', '>', 0)->whereHas('transaction', fn($q) => $q->whereBetween('transaction_date', [$start, $end]))->get();
            foreach ($inflows as $in) {
                fputcsv($file, [$in->transaction->transaction_date->format('Y-m-d'), $in->account->name, $in->debit]);
            }
            fputcsv($file, ['', 'Total Inflow', $inflows->sum('debit')]);
            fputcsv($file, []);

            fputcsv($file, ['Outflows']);
            fputcsv($file, ['Date', 'Account', 'Amount']);
            $outflows = \App\Models\Ledger::whereHas('account', fn($q) => $q->where('name', 'like', '%Cash%')->orWhere('name', 'like', '%Bank%'))
                ->where('credit', '>', 0)->whereHas('transaction', fn($q) => $q->whereBetween('transaction_date', [$start, $end]))->get();
            foreach ($outflows as $out) {
                fputcsv($file, [$out->transaction->transaction_date->format('Y-m-d'), $out->account->name, $out->credit]);
            }
            fputcsv($file, ['', 'Total Outflow', $outflows->sum('credit')]);
            fclose($file);
        };

        return response()->streamDownload($callback, 'cashflow-' . $start . '-' . $end . '.csv');
    }

    protected function exportCashflowPDF($start, $end)
    {
        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('admin.reports.pdf.cashflow', $this->getCashflowData($start, $end));
        return $pdf->download('cashflow-' . $start . '-' . $end . '.pdf');
    }

    protected function exportLedgerCSV($start, $end, $accountId)
    {
        $callback = function () use ($start, $end, $accountId) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Account Ledger', $start . ' to ' . $end]);
            fputcsv($file, []);
            fputcsv($file, ['Date', 'Account', 'Description', 'Debit', 'Credit']);

            $query = \App\Models\Ledger::with(['account', 'transaction'])->whereHas('transaction', fn($q) => $q->whereBetween('transaction_date', [$start, $end]));
            if ($accountId)
                $query->where('account_id', $accountId);

            foreach ($query->lazy() as $l) {
                fputcsv($file, [$l->transaction->transaction_date->format('Y-m-d H:i'), $l->account->name, $l->entry_description ?? $l->transaction->description, $l->debit, $l->credit]);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, 'ledger-' . $start . '-' . $end . '.csv');
    }

    protected function exportLedgerPDF($start, $end, $accountId)
    {
        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('admin.reports.pdf.ledger', $this->getLedgerData($start, $end, $accountId));
        return $pdf->download('ledger-' . $start . '-' . $end . '.pdf');
    }

    protected function exportInvoicesCSV($start, $end)
    {
        $callback = function () use ($start, $end) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Sales Invoices Report', $start . ' to ' . $end]);
            fputcsv($file, []);
            fputcsv($file, ['Inv #', 'Customer', 'Date', 'VAT', 'Total']);

            $invoices = \App\Models\Invoice::whereBetween('issued_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->get();
            foreach ($invoices as $inv) {
                fputcsv($file, [$inv->invoice_number, $inv->customer->name ?? 'Walk-in', $inv->issued_at->format('Y-m-d'), $inv->total_vat_amount, $inv->total_amount]);
            }
            fclose($file);
        };

        return response()->streamDownload($callback, 'invoices-report-' . $start . '-' . $end . '.csv');
    }

    protected function exportInvoicesPDF($start, $end)
    {
        $invoices = \App\Models\Invoice::with('customer')->whereBetween('issued_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->get();
        $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('admin.reports.pdf.invoices', compact('invoices', 'start', 'end'));
        return $pdf->download('sales-invoices-' . $start . '-' . $end . '.pdf');
    }

    private function getVatData($start, $end)
    {
        $outputVat = \App\Models\Invoice::whereBetween('issued_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->sum('total_vat_amount');
        $inputVat = \App\Models\PurchaseOrder::where('status', 'Received')->whereBetween('updated_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->sum('total_vat_amount');
        $netVat = $outputVat - $inputVat;
        $salesDetails = \App\Models\Invoice::whereBetween('issued_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->get();
        $purchaseDetails = \App\Models\PurchaseOrder::where('status', 'Received')->whereBetween('updated_at', [$start . ' 00:00:00', $end . ' 23:59:59'])->get();

        return compact('start', 'end', 'outputVat', 'inputVat', 'netVat', 'salesDetails', 'purchaseDetails');
    }

    private function getCashflowData($start, $end)
    {
        $inflows = \App\Models\Ledger::whereHas('account', fn($q) => $q->where('name', 'like', '%Cash%')->orWhere('name', 'like', '%Bank%'))
            ->where('debit', '>', 0)->whereHas('transaction', fn($q) => $q->whereBetween('transaction_date', [$start, $end]))->with(['transaction', 'account'])->get();

        $outflows = \App\Models\Ledger::whereHas('account', fn($q) => $q->where('name', 'like', '%Cash%')->orWhere('name', 'like', '%Bank%'))
            ->where('credit', '>', 0)->whereHas('transaction', fn($q) => $q->whereBetween('transaction_date', [$start, $end]))->with(['transaction', 'account'])->get();

        $totalInflow = $inflows->sum('debit');
        $totalOutflow = $outflows->sum('credit');
        $netCashflow = $totalInflow - $totalOutflow;

        return compact('start', 'end', 'inflows', 'outflows', 'totalInflow', 'totalOutflow', 'netCashflow');
    }

    private function getLedgerData($start, $end, $accountId)
    {
        $query = \App\Models\Ledger::with(['account', 'transaction'])
            ->whereHas('transaction', fn($q) => $q->whereBetween('transaction_date', [$start, $end]));

        if ($accountId)
            $query->where('account_id', $accountId);

        $ledgers = $query->latest()->get();
        $account = $accountId ? \App\Models\Account::find($accountId) : null;

        return compact('start', 'end', 'ledgers', 'account');
    }
}
