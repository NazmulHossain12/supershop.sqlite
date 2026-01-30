<?php

namespace App\Observers;

use App\Models\PurchaseOrder;

class PurchaseOrderObserver
{
    /**
     * Handle the PurchaseOrder "created" event.
     */
    public function created(PurchaseOrder $purchaseOrder): void
    {
        if ($purchaseOrder->status === 'Received') {
            $this->handleReceived($purchaseOrder);
        }
    }

    /**
     * Handle the PurchaseOrder "updated" event.
     */
    public function updated(PurchaseOrder $purchaseOrder): void
    {
        if ($purchaseOrder->isDirty('status') && $purchaseOrder->status === 'Received') {
            $this->handleReceived($purchaseOrder);
        }
    }

    protected function handleReceived(PurchaseOrder $purchaseOrder): void
    {
        \Illuminate\Support\Facades\DB::transaction(function () use ($purchaseOrder) {
            // 1. Update Stock & Cost
            foreach ($purchaseOrder->items as $item) {
                $product = $item->product;
                if (!$product) continue;

                $currentQty = $product->stock_quantity;
                $currentCost = (float) $product->cost_price;
                $newQty = $item->quantity;
                $newCost = (float) $item->unit_cost;

                if ($currentQty + $newQty > 0) {
                    // Weighted Average Cost Formula
                    $updatedCost = (($currentQty * $currentCost) + ($newQty * $newCost)) / ($currentQty + $newQty);
                    $product->cost_price = round($updatedCost, 2);
                }

                $product->stock_quantity += $newQty;
                $product->save();
            }

            // 2. Update supplier balance (Liability increases)
            if ($purchaseOrder->supplier) {
                $purchaseOrder->supplier->increment('current_balance', $purchaseOrder->total_amount);
            }

            // 3. Accounting Transaction
            $inventoryAccount = \App\Models\Account::where('code', '1200')->first();
            $accountsPayableAccount = \App\Models\Account::where('code', '2100')->first();

            if ($inventoryAccount && $accountsPayableAccount) {
                $transaction = \App\Models\Transaction::create([
                    'transaction_date' => now(),
                    'reference' => 'PO-' . $purchaseOrder->reference_no,
                    'description' => 'Purchase Order Received - Ref: ' . $purchaseOrder->reference_no,
                    'type' => 'Purchase',
                ]);

                $netAmount = $purchaseOrder->total_amount - $purchaseOrder->total_vat_amount;

                // Debit Inventory (Increase Asset) - Net of VAT
                \App\Models\Ledger::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $inventoryAccount->id,
                    'debit' => $netAmount,
                    'credit' => 0,
                ]);
                $inventoryAccount->increment('balance', $netAmount);

                // Debit Sales Tax Payable (VAT Input - Decrease Liability/Increase Asset)
                if ($purchaseOrder->total_vat_amount > 0) {
                    $salesTaxAccount = \App\Models\Account::where('code', '2200')->first();
                    if ($salesTaxAccount) {
                        \App\Models\Ledger::create([
                            'transaction_id' => $transaction->id,
                            'account_id' => $salesTaxAccount->id,
                            'debit' => $purchaseOrder->total_vat_amount,
                            'credit' => 0,
                        ]);
                        // Decreasing balance because it's a liability account being debited (Tax Payable)
                        // Actually, Input Tax is usually an Asset or reduces Liability.
                        // If 2200 is Sales Tax Payable (Liability), debiting it reduces the liability (offsetting output tax).
                        $salesTaxAccount->decrement('balance', $purchaseOrder->total_vat_amount);
                    }
                }

                // Credit Accounts Payable (Increase Liability) - Total Amount Including VAT
                \App\Models\Ledger::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $accountsPayableAccount->id,
                    'debit' => 0,
                    'credit' => $purchaseOrder->total_amount,
                ]);
                $accountsPayableAccount->increment('balance', $purchaseOrder->total_amount);
            }
        });
    }

    /**
     * Handle the PurchaseOrder "deleted" event.
     */
    public function deleted(PurchaseOrder $purchaseOrder): void
    {
        // Prevent deleting Received orders? Or reverse logic?
        // For now, assume deletion is restricted by Policy/Controller.
    }
}
