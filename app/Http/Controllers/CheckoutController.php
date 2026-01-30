<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\OrderConfirmation;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CheckoutController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    public function index()
    {
        $cart = $this->cartService->getCart();

        if (count($cart) == 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $total = $this->cartService->getTotal();
        return view('checkout.index', compact('cart', 'total'));
    }

    public function store(Request $request)
    {
        $cart = $this->cartService->getCart();

        if (count($cart) == 0) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'city' => 'required|string|max:255',
            'state' => 'nullable|string|max:255',
            'zip_code' => 'required|string|max:20',
            'payment_method' => 'required|in:cash_on_delivery,card,paypal',
        ]);

        try {
            DB::beginTransaction();

            $grandTotal = $this->cartService->getTotal();

            // Create Order
            $order = Order::create([
                'user_id' => Auth::id(), // Nullable
                'order_number' => 'ORD-' . strtoupper(Str::random(10)),
                'status' => 'pending',
                'grand_total' => $grandTotal,
                'item_count' => count($cart),
                'is_paid' => false, // Default for COD
                'payment_method' => $validated['payment_method'],
                'first_name' => $validated['first_name'],
                'last_name' => $validated['last_name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'address' => $validated['address'],
                'city' => $validated['city'],
                'state' => $validated['state'],
                'zip_code' => $validated['zip_code'],
                'notes' => $request->input('notes'),
            ]);

            // Create Order Items and calculate VAT
            $totalVat = 0;
            foreach ($cart as $item) {
                $product = \App\Models\Product::find($item['id']);
                $price = $item['price'];
                $vatRate = (float) ($product->vat_rate ?? 0);
                // Calculate VAT from VAT-inclusive price: Price * (Rate / (100 + Rate))
                $vatAmount = ($price * $item['quantity']) * ($vatRate / (100 + $vatRate));
                $totalVat += $vatAmount;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $price,
                    'vat_amount' => $vatAmount,
                ]);

                // Update product stock
                if ($product) {
                    $product->decrement('stock_quantity', $item['quantity']);
                }
            }

            // Clear cart
            session()->forget('cart');

            // Record transaction & high-precision ledger entries
            $transaction = \App\Models\Transaction::create([
                'order_id' => $order->id,
                'type' => 'sale',
                'category' => 'product_sale',
                'amount' => $order->grand_total,
                'description' => 'Order #' . $order->order_number,
                'transaction_date' => now(),
            ]);

            // Double-entry record: Debit Cash/Bank, Credit Sales Revenue, Credit Sales Tax Payable
            // 1002 is Bank Account, 4001 is Sales Revenue, 2200 is Sales Tax Payable
            $bankAccount = \App\Models\Account::where('code', '1002')->first();
            $salesAccount = \App\Models\Account::where('code', '4001')->first();
            $vatAccount = \App\Models\Account::where('code', '2200')->first();

            $netRevenue = (float) $order->grand_total - (float) $totalVat;

            if ($bankAccount && $salesAccount && $vatAccount) {
                \App\Models\Ledger::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $bankAccount->id,
                    'debit' => (float) $order->grand_total,
                    'entry_description' => 'Gross payment received for Order #' . $order->order_number,
                ]);
                $bankAccount->increment('balance', (float) $order->grand_total);

                \App\Models\Ledger::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $salesAccount->id,
                    'credit' => $netRevenue,
                    'entry_description' => 'Net revenue recognized for Order #' . $order->order_number,
                ]);
                $salesAccount->increment('balance', $netRevenue);

                \App\Models\Ledger::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $vatAccount->id,
                    'credit' => (float) $totalVat,
                    'entry_description' => 'VAT liability recorded for Order #' . $order->order_number,
                ]);
                $vatAccount->increment('balance', (float) $totalVat);
            }

            // Generate Invoice and PDF
            $invoice = \App\Models\Invoice::create([
                'order_id' => $order->id,
                'invoice_number' => 'INV-' . strtoupper(Str::random(8)),
                'total_amount' => $order->grand_total,
                'issued_at' => now(),
            ]);

            try {
                $pdf = \Barryvdh\Snappy\Facades\SnappyPdf::loadView('pdf.invoice', ['order' => $order]);
                $pdfDirectory = storage_path('app/public/invoices');
                if (!file_exists($pdfDirectory)) {
                    mkdir($pdfDirectory, 0755, true);
                }
                $pdfPath = 'invoices/' . $invoice->invoice_number . '.pdf';
                $pdf->save(storage_path('app/public/' . $pdfPath));
                $invoice->update(['pdf_path' => $pdfPath]);
            } catch (\Exception $e) {
                \Log::error('PDF generation failed: ' . $e->getMessage());
            }

            DB::commit();

            // Send confirmation email
            Mail::to($order->email)->send(new OrderConfirmation($order));

            return redirect()->route('orders.show', $order)->with('success', 'Order placed successfully! Order #' . $order->order_number . '. Invoice generated.');

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Checkout failed: ' . $e->getMessage());
            return redirect()->route('checkout.index')->with('error', 'Checkout failed. Please try again: ' . $e->getMessage());
        }
    }
}
