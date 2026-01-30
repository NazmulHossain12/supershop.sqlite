<?php

namespace App\Filament\Resources\Invoices\Pages;

use App\Filament\Resources\Invoices\InvoiceResource;
use App\Models\Product;
use Filament\Resources\Pages\CreateRecord;
use Livewire\Attributes\On;

class CreateInvoice extends CreateRecord
{
    protected static string $resource = InvoiceResource::class;

    protected function getHeaderWidgets(): array
    {
        return [
            //
        ];
    }

    #[On('barcode-scanned')]
    public function handleBarcodeScanned($barcode)
    {
        $scanMode = $this->data['scan_mode'] ?? false;

        if (!$scanMode) {
            return;
        }

        $product = Product::where('barcode', $barcode)
            ->orWhere('sku', $barcode)
            ->first();

        if (!$product) {
            $this->addError('data.scan_mode', "Product with barcode {$barcode} not found.");
            return;
        }

        $items = $this->data['items'] ?? [];
        $found = false;

        $vatRate = (float) ($product->vat_rate ?? 0);
        $unitPrice = (float) $product->regular_price;

        foreach ($items as $key => $item) {
            if ($item['product_id'] == $product->id) {
                $items[$key]['quantity']++;
                $items[$key]['subtotal'] = $items[$key]['quantity'] * $items[$key]['unit_price'];

                // Calculate VAT from VAT-inclusive price
                $vatAmount = $items[$key]['subtotal'] * ($vatRate / (100 + $vatRate));
                $items[$key]['vat_amount'] = round($vatAmount, 2);

                $found = true;
                break;
            }
        }

        if (!$found) {
            $vatAmount = ($unitPrice * 1) * ($vatRate / (100 + $vatRate));
            $items[] = [
                'product_id' => $product->id,
                'quantity' => 1,
                'unit_price' => $unitPrice,
                'vat_amount' => round($vatAmount, 2),
                'subtotal' => $unitPrice,
            ];
        }

        $this->data['items'] = $items;

        // Update totals
        $total = 0;
        $totalVat = 0;
        foreach ($items as $item) {
            $total += (float) ($item['subtotal'] ?? 0);
            $totalVat += (float) ($item['vat_amount'] ?? 0);
        }
        $this->data['total_amount'] = round($total, 2);
        $this->data['total_vat_amount'] = round($totalVat, 2);
    }

    protected function afterCreate(): void
    {
        $invoice = $this->record;

        // 1. Reduce Stock
        foreach ($invoice->items as $item) {
            $product = $item->product;
            if ($product) {
                $product->stock_quantity -= $item->quantity;
                $product->save();
            }
        }

        // 2. Accounting Ledger Entries
        $totalVat = (float) ($invoice->total_vat_amount ?? $invoice->items->sum('vat_amount'));
        $netRevenue = (float) $invoice->total_amount - $totalVat;

        $transaction = \App\Models\Transaction::create([
            'order_id' => $invoice->order_id,
            'type' => 'sale',
            'category' => 'pos_sale',
            'amount' => $invoice->total_amount,
            'description' => 'POS Invoice #' . $invoice->invoice_number,
            'transaction_date' => now(),
        ]);

        $bankAccount = \App\Models\Account::where('code', '1002')->first();
        $salesAccount = \App\Models\Account::where('code', '4001')->first();
        $vatAccount = \App\Models\Account::where('code', '2200')->first();

        if ($bankAccount && $salesAccount && $vatAccount) {
            \App\Models\Ledger::create([
                'transaction_id' => $transaction->id,
                'account_id' => $bankAccount->id,
                'debit' => (float) $invoice->total_amount,
                'entry_description' => 'POS payment received for Invoice #' . $invoice->invoice_number,
            ]);
            $bankAccount->increment('balance', (float) $invoice->total_amount);

            \App\Models\Ledger::create([
                'transaction_id' => $transaction->id,
                'account_id' => $salesAccount->id,
                'credit' => $netRevenue,
                'entry_description' => 'POS revenue recognized for Invoice #' . $invoice->invoice_number,
            ]);
            $salesAccount->increment('balance', $netRevenue);

            \App\Models\Ledger::create([
                'transaction_id' => $transaction->id,
                'account_id' => $vatAccount->id,
                'credit' => (float) $totalVat,
                'entry_description' => 'VAT liability for Invoice #' . $invoice->invoice_number,
            ]);
            $vatAccount->increment('balance', (float) $totalVat);
        }
    }

    protected function getHeaderActions(): array
    {
        return [
            //
        ];
    }

    // Inject JS to handle the bridging between the global window event and Livewire
    protected function getFooterWidgets(): array
    {
        return [];
    }

    public function getFooter(): ?\Illuminate\Contracts\View\View
    {
        return view('filament.pages.invoice-footer');
    }
}
