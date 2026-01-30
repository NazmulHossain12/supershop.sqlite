<?php

namespace App\Livewire\Admin;

use Livewire\Component;
use Livewire\Attributes\Layout;
use App\Models\Product;
use App\Models\Category;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Transaction;
use App\Models\Ledger;
use App\Models\Account;
use App\Models\SuspendedSale;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

#[Layout('layouts.app')]
class PosTerminal extends Component
{
    public $search = '';
    public $category_id = '';
    public $cart = [];
    public $discount = 0;
    public $discount_type = 'fixed'; // or 'percentage'
    public $payment_method = 'cash';
    public $amount_paid_cash = 0;
    public $amount_paid_card = 0;
    public $is_split_payment = false;

    public $showChangeModal = false;
    public $showSuspendedModal = false;
    public $showCustomerModal = false;
    public $lastChange = 0;
    public $lastInvoiceId = null;

    // Loyalty Properties
    public $customer_phone = '';
    public $customer_name = '';
    public $selected_customer = null;
    public $points_earned = 0;
    public $redeem_points = 0;
    public $redemption_discount = 0;

    public function mount()
    {
        if (!auth()->user()->can('access_pos')) {
            abort(403, 'Unauthorized access to POS Terminal.');
        }
    }

    public function addToCart($productId)
    {
        $product = Product::find($productId);

        if (!$product || $product->stock_quantity <= 0) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Product out of stock!']);
            return;
        }

        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']++;
        } else {
            $this->cart[$productId] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) ($product->sale_price ?? $product->regular_price),
                'quantity' => 1,
                'vat_rate' => (float) ($product->vat_rate ?? 0),
                'sku' => $product->sku,
            ];
        }

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Added to cart']);
    }

    public function removeFromCart($productId)
    {
        unset($this->cart[$productId]);
    }

    public function updateQuantity($productId, $quantity)
    {
        if ($quantity <= 0) {
            $this->removeFromCart($productId);
            return;
        }

        $product = Product::find($productId);
        if ($product && $quantity > $product->stock_quantity) {
            $this->dispatch('notify', ['type' => 'warning', 'message' => 'Max available stock reached']);
            $this->cart[$productId]['quantity'] = $product->stock_quantity;
            return;
        }

        $this->cart[$productId]['quantity'] = $quantity;
    }

    public function getSubtotalProperty()
    {
        return collect($this->cart)->sum(fn($item) => $item['price'] * $item['quantity']);
    }

    public function getVatTotalProperty()
    {
        return collect($this->cart)->sum(function ($item) {
            $vatRate = $item['vat_rate'];
            // Price is VAT inclusive: Price * (Rate / (100 + Rate))
            return ($item['price'] * $item['quantity']) * ($vatRate / (100 + $vatRate));
        });
    }

    public function getDiscountAmountProperty()
    {
        if ($this->discount_type === 'percentage') {
            return $this->subtotal * ($this->discount / 100);
        }
        return (float) $this->discount;
    }

    public function getGrandTotalProperty()
    {
        return max(0, $this->subtotal - $this->discountAmount - $this->redemption_discount);
    }

    public function applyRedemption()
    {
        if (!$this->selected_customer)
            return;

        $availablePoints = $this->selected_customer['loyalty_points_balance'];
        $redemptionValue = (float) \App\Models\Setting::get('redemption_value', 100);

        // Calculate max points that can be used (can't exceed grand total before this discount)
        $maxCurrencyDiscount = $this->subtotal - $this->discountAmount;
        $maxPointsNeeded = floor($maxCurrencyDiscount * $redemptionValue);

        $pointsToUse = min($availablePoints, $maxPointsNeeded);

        $this->redeem_points = $pointsToUse;
        $this->redemption_discount = $pointsToUse / $redemptionValue;

        $this->dispatch('notify', ['type' => 'success', 'message' => 'Redeemed ' . $pointsToUse . ' points for $' . number_format($this->redemption_discount, 2)]);
    }

    public function cancelRedemption()
    {
        $this->redeem_points = 0;
        $this->redemption_discount = 0;
    }

    public function checkout()
    {
        if (empty($this->cart))
            return;

        DB::beginTransaction();
        try {
            $invoice = Invoice::create([
                'invoice_number' => 'POS-' . strtoupper(Str::random(8)),
                'total_amount' => $this->grandTotal,
                'total_vat_amount' => $this->vatTotal,
                'issued_at' => now(),
                'payment_method' => $this->payment_method,
            ]);

            foreach ($this->cart as $item) {
                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['id'],
                    'quantity' => $item['quantity'],
                    'price' => $item['price'],
                    'vat_amount' => ($item['price'] * $item['quantity']) * ($item['vat_rate'] / (100 + $item['vat_rate'])),
                ]);

                // Reduce Stock
                Product::where('id', $item['id'])->decrement('stock_quantity', $item['quantity']);
            }

            // Ledger Entries
            $this->recordAccounting($invoice);

            $this->lastInvoiceId = $invoice->id;

            $totalPaid = (float) $this->amount_paid_cash + (float) $this->amount_paid_card;
            if (!$this->is_split_payment) {
                $totalPaid = ($this->payment_method === 'cash') ? (float) $this->amount_paid_cash : (float) $this->grandTotal;
            }

            $this->lastChange = max(0, $totalPaid - $this->grandTotal);

            // Award Loyalty Points
            if ($this->selected_customer) {
                $pointsPerUnit = (float) \App\Models\Setting::get('points_per_currency_unit', 1);
                $this->points_earned = floor($this->grandTotal * $pointsPerUnit);

                if ($this->points_earned > 0) {
                    $customer = \App\Models\User::find($this->selected_customer['id']);
                    $customer->awardPoints(
                        $this->points_earned,
                        'Purchase at POS - Inv: ' . $invoice->invoice_number,
                        $invoice->id
                    );
                }

                // Handle Redemption
                if ($this->redeem_points > 0) {
                    $customer->loyaltyTransactions()->create([
                        'points' => $this->redeem_points,
                        'type' => 'redeem',
                        'description' => 'Point Redemption at POS - Inv: ' . $invoice->invoice_number,
                        'invoice_id' => $invoice->id,
                    ]);
                    $customer->decrement('loyalty_points_balance', $this->redeem_points);
                }
            }

            DB::commit();

            $this->resetSale();
            $this->showChangeModal = true;

            $this->dispatch('notify', ['type' => 'success', 'message' => 'Transaction Completed!']);

        } catch (\Exception $e) {
            DB::rollBack();
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Transaction Failed: ' . $e->getMessage()]);
        }
    }

    public function resetSale()
    {
        $this->cart = [];
        $this->discount = 0;
        $this->amount_paid_cash = 0;
        $this->amount_paid_card = 0;
        $this->selected_customer = null;
        $this->customer_phone = '';
        $this->redeem_points = 0;
        $this->redemption_discount = 0;
        $this->is_split_payment = false;
        $this->payment_method = 'cash';
    }

    public function updatedCustomerPhone()
    {
        if (strlen($this->customer_phone) >= 10) {
            $customer = \App\Models\User::where('phone', $this->customer_phone)->first();
            if ($customer) {
                $this->selected_customer = $customer->toArray();
                $this->dispatch('notify', ['type' => 'success', 'message' => 'Customer Found: ' . $customer->name]);
            } else {
                $this->showCustomerModal = true;
            }
        }
    }

    public function quickCreateCustomer()
    {
        $this->validate([
            'customer_name' => 'required|string|max:255',
            'customer_phone' => 'required|unique:users,phone',
        ]);

        $customer = \App\Models\User::create([
            'name' => $this->customer_name,
            'phone' => $this->customer_phone,
            'password' => bcrypt(Str::random(12)), // Random password for quick create
        ]);

        $customer->assignRole('Customer');

        $this->selected_customer = $customer->toArray();
        $this->showCustomerModal = false;
        $this->customer_name = '';
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Customer Created Successfully']);
    }

    public function deselectCustomer()
    {
        $this->selected_customer = null;
        $this->customer_phone = '';
    }

    protected function recordAccounting($invoice)
    {
        $transaction = Transaction::create([
            'invoice_id' => $invoice->id,
            'type' => 'sale',
            'category' => 'pos_sale',
            'amount' => $invoice->total_amount,
            'description' => 'POS Transaction ' . $invoice->invoice_number,
            'transaction_date' => now(),
        ]);

        if ($this->is_split_payment) {
            $cashAmount = (float) $this->amount_paid_cash;
            $cardAmount = (float) $this->grandTotal - $cashAmount;
            if ($cardAmount < 0)
                $cardAmount = 0;
        } else {
            $cashAmount = $this->payment_method === 'cash' ? $invoice->total_amount : 0;
            $cardAmount = $this->payment_method === 'card' ? $invoice->total_amount : 0;
        }

        $cashAccount = Account::where('code', '1001')->first(); // Cash
        $bankAccount = Account::where('code', '1002')->first(); // Card/Bank
        $salesAccount = Account::where('code', '4001')->first();
        $vatAccount = Account::where('code', '2200')->first();

        if ($salesAccount && $vatAccount) {
            // Debit Assets
            if ($cashAmount > 0 && $cashAccount) {
                Ledger::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $cashAccount->id,
                    'debit' => $cashAmount,
                    'entry_description' => 'POS Sale Collection (Cash)',
                ]);
                $cashAccount->increment('balance', $cashAmount);
            }

            if ($cardAmount > 0 && $bankAccount) {
                Ledger::create([
                    'transaction_id' => $transaction->id,
                    'account_id' => $bankAccount->id,
                    'debit' => $cardAmount,
                    'entry_description' => 'POS Sale Collection (Card)',
                ]);
                $bankAccount->increment('balance', $cardAmount);
            }

            // Credit Sales Revenue (Net)
            $netRevenue = $invoice->total_amount - $invoice->total_vat_amount;
            Ledger::create([
                'transaction_id' => $transaction->id,
                'account_id' => $salesAccount->id,
                'credit' => $netRevenue,
                'entry_description' => 'POS Sale Revenue',
            ]);
            $salesAccount->increment('balance', $netRevenue);

            // Credit VAT Payable
            Ledger::create([
                'transaction_id' => $transaction->id,
                'account_id' => $vatAccount->id,
                'credit' => $invoice->total_vat_amount,
                'entry_description' => 'VAT on POS Sale',
            ]);
            $vatAccount->increment('balance', $invoice->total_vat_amount);
        }
    }

    public function holdSale($reference = '')
    {
        if (empty($this->cart))
            return;

        SuspendedSale::create([
            'user_id' => auth()->id(),
            'reference' => $reference ?: 'Sale ' . now()->format('H:i'),
            'cart_data' => $this->cart,
            'total_amount' => $this->grandTotal,
        ]);

        $this->cart = [];
        $this->dispatch('notify', ['type' => 'info', 'message' => 'Sale Suspended']);
    }

    public function resumeSale($id)
    {
        $suspended = SuspendedSale::findOrFail($id);
        $this->cart = $suspended->cart_data;
        $suspended->delete();
        $this->showSuspendedModal = false;
        $this->dispatch('notify', ['type' => 'success', 'message' => 'Sale Resumed']);
    }

    public function printLastReceipt()
    {
        $lastInvoice = Invoice::latest()->first();
        if ($lastInvoice) {
            $this->lastInvoiceId = $lastInvoice->id;
            $this->showChangeModal = true;
        } else {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'No recent invoice found']);
        }
    }

    public function render()
    {
        $query = Product::query()
            ->where('status', true);

        if ($this->search) {
            $query->where(function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%')
                    ->orWhere('sku', 'like', '%' . $this->search . '%');
            });
        }

        if ($this->category_id) {
            $query->where('category_id', $this->category_id);
        }

        return view('livewire.admin.pos-terminal', [
            'products' => $query->latest()->limit(20)->get(),
            'categories' => Category::all(),
            'suspendedSales' => SuspendedSale::all(),
        ]);
    }
}
