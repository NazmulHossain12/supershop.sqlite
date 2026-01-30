<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PurchaseOrder;
use App\Models\Supplier;
use App\Models\Product;
use App\Models\PurchaseOrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Support\Number;

class PurchaseOrderController extends Controller
{
    public function index()
    {
        $purchaseOrders = PurchaseOrder::with('supplier')->latest()->paginate(10);
        return view('admin.purchase-orders.index', compact('purchaseOrders'));
    }

    public function create()
    {
        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::where('status', true)->orderBy('name')->get();
        return view('admin.purchase-orders.create', compact('suppliers', 'products'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'reference_no' => 'required|string|unique:purchase_orders,reference_no',
            'expected_delivery_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $totalVatAmount = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $vatRate = (float) ($product->vat_rate ?? 0);
                $subtotal = $item['quantity'] * $item['unit_cost'];
                $vatAmount = $subtotal * ($vatRate / (100 + $vatRate));

                $totalAmount += $subtotal;
                $totalVatAmount += $vatAmount;

                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'vat_amount' => round($vatAmount, 2),
                    'subtotal' => $subtotal,
                ];
            }

            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $validated['supplier_id'],
                'reference_no' => $validated['reference_no'],
                'expected_delivery_date' => $validated['expected_delivery_date'],
                'status' => 'Draft',
                'total_amount' => $totalAmount,
                'total_vat_amount' => round($totalVatAmount, 2),
                'paid_amount' => 0,
            ]);

            foreach ($itemsData as $itemData) {
                $itemData['purchase_order_id'] = $purchaseOrder->id;
                PurchaseOrderItem::create($itemData);
            }

            DB::commit();
            return redirect()->route('admin.purchase-orders.index')->with('success', 'Purchase Order created successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to create purchase order: ' . $e->getMessage())->withInput();
        }
    }

    public function show(PurchaseOrder $purchaseOrder)
    {
        $purchaseOrder->load(['supplier', 'items.product']);
        return view('admin.purchase-orders.show', compact('purchaseOrder'));
    }

    public function edit(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'Draft') {
            return redirect()->route('admin.purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only draft orders can be edited.');
        }

        $suppliers = Supplier::orderBy('name')->get();
        $products = Product::where('status', true)->orderBy('name')->get();
        $purchaseOrder->load('items');

        return view('admin.purchase-orders.edit', compact('purchaseOrder', 'suppliers', 'products'));
    }

    public function update(Request $request, PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'Draft') {
            return redirect()->route('admin.purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only draft orders can be updated.');
        }

        $validated = $request->validate([
            'supplier_id' => 'required|exists:suppliers,id',
            'reference_no' => 'required|string|unique:purchase_orders,reference_no,' . $purchaseOrder->id,
            'expected_delivery_date' => 'nullable|date',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_cost' => 'required|numeric|min:0',
        ]);

        try {
            DB::beginTransaction();

            $totalAmount = 0;
            $totalVatAmount = 0;
            $itemsData = [];

            foreach ($validated['items'] as $item) {
                $product = Product::find($item['product_id']);
                $vatRate = (float) ($product->vat_rate ?? 0);
                $subtotal = $item['quantity'] * $item['unit_cost'];
                $vatAmount = $subtotal * ($vatRate / (100 + $vatRate));

                $totalAmount += $subtotal;
                $totalVatAmount += $vatAmount;

                $itemsData[] = [
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'vat_amount' => round($vatAmount, 2),
                    'subtotal' => $subtotal,
                ];
            }

            $purchaseOrder->update([
                'supplier_id' => $validated['supplier_id'],
                'reference_no' => $validated['reference_no'],
                'expected_delivery_date' => $validated['expected_delivery_date'],
                'total_amount' => $totalAmount,
                'total_vat_amount' => round($totalVatAmount, 2),
            ]);

            // Simple approach: delete old items and create new ones
            $purchaseOrder->items()->delete();

            foreach ($itemsData as $itemData) {
                $itemData['purchase_order_id'] = $purchaseOrder->id;
                PurchaseOrderItem::create($itemData);
            }

            DB::commit();
            return redirect()->route('admin.purchase-orders.show', $purchaseOrder)
                ->with('success', 'Purchase Order updated successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to update purchase order: ' . $e->getMessage())->withInput();
        }
    }

    public function destroy(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'Draft' && $purchaseOrder->status !== 'Cancelled') {
            return redirect()->route('admin.purchase-orders.show', $purchaseOrder)
                ->with('error', 'Only draft or cancelled orders can be deleted.');
        }

        $purchaseOrder->delete(); // Cascading delete should handle items
        return redirect()->route('admin.purchase-orders.index')
            ->with('success', 'Purchase Order deleted successfully.');
    }

    public function updateStatus(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'status' => 'required|in:Draft,Ordered,Received,Cancelled',
        ]);

        $oldStatus = $purchaseOrder->status;
        $purchaseOrder->update(['status' => $validated['status']]);

        // Logic for when order is received (increase stock, update supplier balance, and record accounting)
        if ($validated['status'] === 'Received' && $oldStatus !== 'Received') {
            DB::transaction(function () use ($purchaseOrder) {
                foreach ($purchaseOrder->items as $item) {
                    $product = $item->product;
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

                // Update supplier balance (Liability increases)
                $purchaseOrder->supplier->increment('current_balance', $purchaseOrder->total_amount);

                // Accounting Transaction
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
                            // Decreasing balance because it's a liability account being debited
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

        return redirect()->route('admin.purchase-orders.show', $purchaseOrder)->with('success', 'Order status updated successfully.');
    }

    public function addPayment(Request $request, PurchaseOrder $purchaseOrder)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'required|string',
            'payment_date' => 'required|date',
            'reference_no' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        $balance = $purchaseOrder->total_amount - $purchaseOrder->paid_amount;
        if ($validated['amount'] > $balance) {
            return back()->with('error', 'Payment amount cannot exceed the remaining balance: ' . Number::currency($balance));
        }

        try {
            DB::beginTransaction();

            // 1. Create Purchase Payment
            $payment = \App\Models\PurchasePayment::create([
                'purchase_order_id' => $purchaseOrder->id,
                'supplier_id' => $purchaseOrder->supplier_id,
                'amount' => $validated['amount'],
                'payment_method' => $validated['payment_method'],
                'payment_date' => $validated['payment_date'],
                'reference_no' => $validated['reference_no'],
                'notes' => $validated['notes'],
            ]);

            // 2. Update Purchase Order paid_amount
            $purchaseOrder->increment('paid_amount', $validated['amount']);

            // 3. Update Supplier balance (Liability decreases)
            $purchaseOrder->supplier->decrement('current_balance', $validated['amount']);

            // 4. Accounting Transaction
            $cashAccount = \App\Models\Account::where('code', $validated['payment_method'] === 'Cash' ? '1001' : '1002')->first();
            $accountsPayableAccount = \App\Models\Account::where('code', '2100')->first();

            if ($cashAccount && $accountsPayableAccount) {
                $transaction = \App\Models\Transaction::create([
                    'transaction_date' => $validated['payment_date'],
                    'reference' => $validated['reference_no'] ?? 'PAY-' . strtoupper(Str::random(8)),
                    'description' => 'Supplier Payment - PO: ' . $purchaseOrder->reference_no,
                    'type' => 'Payment',
                ]);

                // Debit Accounts Payable (Decrease Liability)
                \App\Models\Ledger::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $accountsPayableAccount->id,
                    'debit' => $validated['amount'],
                    'credit' => 0,
                ]);
                $accountsPayableAccount->decrement('balance', $validated['amount']);

                // Credit Cash/Bank (Decrease Asset)
                \App\Models\Ledger::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $cashAccount->id,
                    'debit' => 0,
                    'credit' => $validated['amount'],
                ]);
                $cashAccount->decrement('balance', $validated['amount']);
            }

            DB::commit();
            return back()->with('success', 'Payment added successfully.');

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Failed to add payment: ' . $e->getMessage());
        }
    }
}
