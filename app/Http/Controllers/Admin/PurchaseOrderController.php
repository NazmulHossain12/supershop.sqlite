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
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['unit_cost'];
            }

            $purchaseOrder = PurchaseOrder::create([
                'supplier_id' => $validated['supplier_id'],
                'reference_no' => $validated['reference_no'],
                'expected_delivery_date' => $validated['expected_delivery_date'],
                'status' => 'Draft',
                'total_amount' => $totalAmount,
                'paid_amount' => 0,
            ]);

            foreach ($validated['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $item['quantity'] * $item['unit_cost'],
                ]);
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
            foreach ($validated['items'] as $item) {
                $totalAmount += $item['quantity'] * $item['unit_cost'];
            }

            $purchaseOrder->update([
                'supplier_id' => $validated['supplier_id'],
                'reference_no' => $validated['reference_no'],
                'expected_delivery_date' => $validated['expected_delivery_date'],
                'total_amount' => $totalAmount,
            ]);

            // Simple approach: delete old items and create new ones
            $purchaseOrder->items()->delete();

            foreach ($validated['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity' => $item['quantity'],
                    'unit_cost' => $item['unit_cost'],
                    'subtotal' => $item['quantity'] * $item['unit_cost'],
                ]);
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
                    $item->product->increment('stock_quantity', $item->quantity);
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

                    // Debit Inventory (Increase Asset)
                    \App\Models\Ledger::create([
                        'transaction_id' => $transaction->id,
                        'account_id' => $inventoryAccount->id,
                        'debit' => $purchaseOrder->total_amount,
                        'credit' => 0,
                    ]);
                    $inventoryAccount->increment('balance', $purchaseOrder->total_amount);

                    // Credit Accounts Payable (Increase Liability)
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
}
