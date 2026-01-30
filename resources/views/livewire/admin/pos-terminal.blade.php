<div class="flex flex-col h-screen bg-gray-100 overflow-hidden" x-data="{ 
    amountPaidCash: @entangle('amount_paid_cash'),
    amountPaidCard: @entangle('amount_paid_card'),
    isSplit: @entangle('is_split_payment'),
    grandTotal: @entangle('grandTotal'),
    get changeDue() {
        if (!this.isSplit) {
            return Math.max(0, this.amountPaidCash - this.grandTotal).toFixed(2);
        }
        return Math.max(0, (parseFloat(this.amountPaidCash || 0) + parseFloat(this.amountPaidCard || 0)) - this.grandTotal).toFixed(2);
    }
}">
    <!-- Header -->
    <header class="bg-indigo-700 text-white p-4 flex justify-between items-center shadow-lg">
        <div class="flex items-center space-x-4">
            <h1 class="text-2xl font-bold tracking-tight">SUPERSHOP POS</h1>
            <div class="bg-indigo-800 px-3 py-1 rounded text-xs uppercase font-bold text-indigo-200">Terminal #01</div>
        </div>
        <div class="flex items-center space-x-6">
            <div class="text-right">
                <p class="text-xs text-indigo-200 uppercase">Cashier</p>
                <p class="font-bold">{{ auth()->user()->name }}</p>
            </div>
            <button onclick="window.location.reload()" class="p-2 hover:bg-indigo-600 rounded-full transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                    </path>
                </svg>
            </button>
            <a href="{{ route('admin.dashboard') }}"
                class="bg-indigo-800 px-4 py-2 rounded font-bold hover:bg-indigo-900 transition">Exit</a>
        </div>
    </header>

    <!-- Main Content -->
    <main class="flex-1 flex overflow-hidden">

        <!-- Left Side: Products -->
        <div class="w-2/3 flex flex-col p-4 space-y-4 border-r bg-white">
            <div class="flex space-x-4">
                <div class="relative flex-1">
                    <input type="text" wire:model.live.debounce.300ms="search" id="pos-search-input"
                        placeholder="Search product (F1) or scan barcode..."
                        class="w-full pl-10 pr-4 py-3 bg-gray-100 border-none rounded-xl focus:ring-2 focus:ring-indigo-500 text-lg">
                    <svg class="w-6 h-6 absolute left-3 top-3.5 text-gray-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <select wire:model.live="category_id"
                    class="bg-gray-100 border-none rounded-xl focus:ring-2 focus:ring-indigo-500">
                    <option value="">All Categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}">{{ $category->name }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Product Grid -->
            <div class="flex-1 overflow-y-auto pr-2">
                <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
                    @foreach($products as $product)
                        <div wire:click="addToCart({{ $product->id }})"
                            class="group cursor-pointer bg-white border border-gray-100 rounded-2xl overflow-hidden shadow-sm hover:shadow-md transition-all active:scale-95 relative {{ $product->stock_quantity <= 0 ? 'opacity-50 pointer-events-none' : '' }}">
                            <div class="aspect-square bg-gray-50 relative">
                                <img src="{{ $product->primary_image_url }}" alt="{{ $product->name }}"
                                    class="w-full h-full object-cover">
                                @if($product->stock_quantity <= 5 && $product->stock_quantity > 0)
                                    <span
                                        class="absolute top-2 right-2 bg-orange-500 text-white text-[10px] px-2 py-0.5 rounded-full font-bold uppercase">Low
                                        Stock</span>
                                @endif
                                @if($product->stock_quantity <= 0)
                                    <span
                                        class="absolute inset-0 bg-black/40 flex items-center justify-center text-white font-bold uppercase text-xs">Out
                                        of Stock</span>
                                @endif
                            </div>
                            <div class="p-3">
                                <h3 class="text-sm font-bold text-gray-800 line-clamp-1 group-hover:text-indigo-600">
                                    {{ $product->name }}
                                </h3>
                                <div class="flex justify-between items-center mt-2">
                                    <span
                                        class="text-indigo-600 font-extrabold">{{ Number::currency($product->sale_price ?? $product->regular_price) }}</span>
                                    <span class="text-[10px] text-gray-400 font-bold uppercase tracking-tighter">Qty:
                                        {{ $product->stock_quantity }}</span>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Right Side: Cart -->
        <div class="w-1/3 flex flex-col bg-gray-50">
            <!-- Customer Lookup -->
            <div class="p-4 border-b bg-indigo-50">
                @if(!$selected_customer)
                    <div class="relative">
                        <input type="text" wire:model.live.debounce.500ms="customer_phone"
                            placeholder="Customer Phone (Lookup/Create)..."
                            class="w-full pl-10 pr-4 py-2 bg-white border-2 border-indigo-100 rounded-xl focus:ring-2 focus:ring-indigo-500 text-sm font-bold">
                        <svg class="w-5 h-5 absolute left-3 top-2.5 text-indigo-400" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                    </div>
                @else
                    <div class="flex items-center justify-between bg-white p-3 rounded-xl border-2 border-indigo-500">
                        <div class="flex items-center space-x-3">
                            <div class="w-10 h-10 bg-indigo-100 rounded-full flex items-center justify-center text-indigo-700 font-bold">
                                {{ substr($selected_customer['name'], 0, 1) }}
                            </div>
                            <div>
                                <p class="text-xs font-bold text-gray-900">{{ $selected_customer['name'] }}</p>
                                <p class="text-[10px] text-indigo-600 font-bold uppercase">{{ $selected_customer['loyalty_points_balance'] }} Points</p>
                            </div>
                        </div>
                        <div class="flex items-center space-x-2">
                            @if($selected_customer['loyalty_points_balance'] > 0 && $redemption_discount == 0)
                                <button wire:click="applyRedemption" class="bg-green-100 text-green-700 px-2 py-1 rounded text-[10px] font-bold hover:bg-green-200 transition">REDEEM</button>
                            @elseif($redemption_discount > 0)
                                <button wire:click="cancelRedemption" class="text-red-500 hover:text-red-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            @endif
                            <button wire:click="deselectCustomer" class="text-gray-400 hover:text-red-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>
                @endif
            </div>

            <div class="p-4 border-b bg-white flex justify-between items-center">
                <h2 class="text-lg font-bold flex items-center">
                    <svg class="w-5 h-5 mr-2 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                    Current Cart
                </h2>
                <button wire:click="$toggle('showSuspendedModal')"
                    class="text-xs font-bold text-indigo-600 hover:underline uppercase tracking-widest">Held Sales
                    ({{ $suspendedSales->count() }})</button>
            </div>

            <div class="flex-1 overflow-y-auto p-4 space-y-3">
                @forelse($cart as $id => $item)
                    <div
                        class="bg-white p-3 rounded-xl shadow-sm border border-gray-100 flex items-center justify-between group">
                        <div class="flex-1">
                            <h4 class="text-xs font-bold text-gray-800 line-clamp-1">{{ $item['name'] }}</h4>
                            <p class="text-[10px] text-gray-400 font-mono">{{ $item['sku'] }}</p>
                            <div class="flex items-center mt-2 space-x-3">
                                <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] - 1 }})"
                                    class="p-1 bg-gray-100 rounded text-gray-500 hover:bg-gray-200">-</button>
                                <span class="text-sm font-bold w-4 text-center">{{ $item['quantity'] }}</span>
                                <button wire:click="updateQuantity({{ $id }}, {{ $item['quantity'] + 1 }})"
                                    class="p-1 bg-gray-100 rounded text-gray-500 hover:bg-gray-200">+</button>
                            </div>
                        </div>
                        <div class="text-right pl-4">
                            <p class="font-bold text-gray-900">{{ Number::currency($item['price'] * $item['quantity']) }}
                            </p>
                            <button wire:click="removeFromCart({{ $id }})"
                                class="text-[10px] text-red-500 font-bold uppercase tracking-widest mt-1 opacity-0 group-hover:opacity-100 transition">Remove</button>
                        </div>
                    </div>
                @empty
                    <div class="flex flex-col items-center justify-center h-full text-gray-400 space-y-4">
                        <svg class="w-16 h-16 opacity-20" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <p class="text-sm font-medium">Cart is empty. Start scanning!</p>
                    </div>
                @endforelse
            </div>

            <!-- Footer Stats -->
            <div class="bg-white p-6 shadow-[0_-10px_20px_rgba(0,0,0,0.05)] space-y-4">
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between text-gray-500">
                        <span>Subtotal</span>
                        <span class="font-bold">{{ Number::currency($this->subtotal) }}</span>
                    </div>
                    <div class="flex justify-between text-gray-500">
                        <span>VAT (Included)</span>
                        <span class="font-bold">{{ Number::currency($this->vatTotal) }}</span>
                    </div>
                    @if($redemption_discount > 0)
                        <div class="flex justify-between text-green-600 font-bold">
                            <span>Point Redemption</span>
                            <span>-{{ Number::currency($this->redemption_discount) }}</span>
                        </div>
                    @endif
                    <div class="flex justify-between items-center py-2 border-t border-b border-gray-100">
                        <span class="text-gray-500">Discount</span>
                        <div class="flex items-center space-x-2">
                            <select wire:model.live="discount_type" class="text-xs bg-gray-100 border-none rounded p-1">
                                <option value="fixed">$</option>
                                <option value="percentage">%</option>
                            </select>
                            <input type="number" wire:model.live="discount"
                                class="w-16 p-1 text-right bg-gray-100 border-none rounded text-sm font-bold">
                        </div>
                    </div>
                </div>

                <div class="flex justify-between items-end">
                    <span class="text-gray-500 font-bold uppercase tracking-widest text-xs">Grand Total</span>
                    <span
                        class="text-4xl font-extrabold text-indigo-700 leading-none">{{ Number::currency($this->grandTotal) }}</span>
                </div>

                <!-- Payment Entry -->
                <div class="pt-4 space-y-3">
                    <div class="flex justify-between items-center bg-gray-100 p-2 rounded-xl">
                        <span class="text-xs font-bold text-gray-500 uppercase ml-2">Split Payment</span>
                        <button wire:click="$toggle('is_split_payment')"
                            class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none {{ $is_split_payment ? 'bg-indigo-600' : 'bg-gray-200' }}">
                            <span
                                class="inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out {{ $is_split_payment ? 'translate-x-5' : 'translate-x-0' }}"></span>
                        </button>
                    </div>

                    @if(!$is_split_payment)
                        <div class="flex gap-2">
                            <button wire:click="$set('payment_method', 'cash')"
                                class="flex-1 py-3 rounded-xl border-2 font-bold transition {{ $payment_method === 'cash' ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-100 text-gray-500' }}">CASH</button>
                            <button wire:click="$set('payment_method', 'card')"
                                class="flex-1 py-3 rounded-xl border-2 font-bold transition {{ $payment_method === 'card' ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-100 text-gray-500' }}">CARD</button>
                        </div>

                        @if($payment_method === 'cash')
                            <div class="relative">
                                <input type="number" wire:model.live="amount_paid_cash" placeholder="Amount Paid by Customer"
                                    class="w-full py-4 px-4 bg-gray-100 border-none rounded-xl text-xl font-bold focus:ring-2 focus:ring-green-500">
                                <span class="absolute right-4 top-4 text-gray-400 font-bold">$</span>
                            </div>
                        @endif
                    @else
                        <div class="space-y-2">
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-[10px] font-bold text-gray-400 uppercase">Cash
                                    Amount</span>
                                <input type="number" wire:model.live="amount_paid_cash"
                                    class="w-full pt-6 pb-2 px-3 bg-gray-100 border-none rounded-xl text-lg font-bold">
                            </div>
                            <div class="relative">
                                <span class="absolute left-3 top-2 text-[10px] font-bold text-gray-400 uppercase">Card
                                    Amount</span>
                                <input type="number" wire:model.live="amount_paid_card"
                                    class="w-full pt-6 pb-2 px-3 bg-gray-100 border-none rounded-xl text-lg font-bold">
                            </div>
                        </div>
                    @endif

                    <div class="flex gap-2">
                        <button wire:click="holdSale"
                            class="px-4 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">HOLD</button>
                        <button wire:click="checkout"
                            class="flex-1 py-4 bg-indigo-600 text-white rounded-xl font-extrabold text-xl shadow-lg shadow-indigo-200 hover:bg-indigo-700 transition active:scale-[0.98] disabled:opacity-50"
                            @if(empty($cart)) disabled @endif>
                            PAY NOW (F2)
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <!-- Modals -->
    @if($showChangeModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white w-full max-w-sm rounded-3xl p-8 text-center shadow-2xl border border-gray-100">
                <div
                    class="w-20 h-20 bg-green-100 text-green-600 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                    </svg>
                </div>
                <h2 class="text-2xl font-black text-gray-900 mb-2">Transaction Successful</h2>
                
                @if($points_earned > 0)
                    <div class="mb-6 bg-indigo-50 p-3 rounded-xl border border-indigo-100">
                        <p class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Loyalty Points Earned</p>
                        <p class="text-2xl font-black text-indigo-700">+{{ $points_earned }}</p>
                    </div>
                @endif

                <p class="text-gray-500 mb-8 uppercase tracking-widest text-[10px] font-bold">Return Change to Customer</p>

                <div class="bg-gray-50 p-6 rounded-2xl mb-8 border border-gray-100">
                    <p class="text-5xl font-black text-green-600">{{ Number::currency($lastChange) }}</p>
                </div>

                <div class="space-y-3">
                    <button onclick="window.print()"
                        class="w-full py-4 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition shadow-lg shadow-indigo-100">PRINT
                        RECEIPT</button>
                    <button wire:click="resetSale; $set('showChangeModal', false)"
                        class="w-full py-4 bg-gray-100 text-gray-600 rounded-xl font-bold hover:bg-gray-200 transition">NEW
                        SALE</button>
                </div>
            </div>
        </div>
    @endif

    <!-- Quick Create Customer Modal -->
    @if($showCustomerModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white w-full max-w-sm rounded-3xl p-8 shadow-2xl border border-gray-100">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-indigo-100 text-indigo-600 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-black text-gray-900">New Customer?</h2>
                    <p class="text-gray-500 text-sm">Phone number not found. Create a quick profile!</p>
                </div>

                <div class="space-y-4">
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase ml-2">Full Name</label>
                        <input type="text" wire:model="customer_name"
                            class="w-full py-3 px-4 bg-gray-100 border-none rounded-xl font-bold focus:ring-2 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="text-[10px] font-bold text-gray-400 uppercase ml-2">Phone Number</label>
                        <input type="text" wire:model="customer_phone" readonly
                            class="w-full py-3 px-4 bg-gray-50 border-none rounded-xl font-bold text-gray-400">
                    </div>

                    <div class="pt-4 space-y-3">
                        <button wire:click="quickCreateCustomer"
                            class="w-full py-4 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 transition">CREATE & SELECT</button>
                        <button wire:click="$set('showCustomerModal', false)"
                            class="w-full py-4 text-gray-400 font-bold hover:text-gray-600 transition">CANCEL</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    @if($showSuspendedModal)
        <div class="fixed inset-0 bg-black/60 backdrop-blur-sm flex items-center justify-center z-50 p-4">
            <div class="bg-white w-full max-w-2xl rounded-3xl p-8 shadow-2xl">
                <div class="flex justify-between items-center mb-6">
                    <h2 class="text-2xl font-black text-gray-900">Held Sales</h2>
                    <button wire:click="$set('showSuspendedModal', false)"
                        class="text-gray-400 hover:text-gray-600">&times;</button>
                </div>

                <div class="space-y-4 max-h-[60vh] overflow-y-auto pr-2">
                    @forelse($suspendedSales as $sale)
                        <div class="bg-gray-50 p-4 rounded-2xl flex justify-between items-center border border-gray-100">
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $sale->reference }}</h4>
                                <p class="text-xs text-gray-500">{{ $sale->created_at->diffForHumans() }} •
                                    {{ count($sale->cart_data) }} items
                                </p>
                            </div>
                            <div class="flex items-center space-x-4">
                                <span class="font-extrabold text-indigo-600">{{ Number::currency($sale->total_amount) }}</span>
                                <button wire:click="resumeSale({{ $sale->id }})"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-indigo-700 transition">RESUME</button>
                            </div>
                        </div>
                    @empty
                        <p class="text-center text-gray-400 py-12">No suspended sales found.</p>
                    @endforelse
                </div>
            </div>
        </div>
    @endif

    <!-- Print Only Section -->
    <div id="thermal-receipt" class="hidden print:block font-mono text-[10px] leading-tight w-[58mm] mx-auto py-4">
        <div class="text-center mb-4">
            <h2 class="text-xs font-bold uppercase">SUPERSHOP</h2>
            <p>123 Ecommerce Street</p>
            <p>Tech City, TC 99999</p>
            <p>Tel: +1 234 567 890</p>
        </div>

        <div class="border-b border-black border-dashed mb-2 pb-2">
            @if($lastInvoiceId)
                @php $inv = \App\Models\Invoice::find($lastInvoiceId); @endphp
                <p>Invoice: {{ $inv?->invoice_number }}</p>
                <p>Date: {{ now()->format('Y-m-d H:i') }}</p>
                <p>Cashier: {{ auth()->user()->name }}</p>
            @endif
        </div>

        <table class="w-full mb-2">
            <tbody>
                @foreach($cart as $item)
                    <tr>
                        <td colspan="2">{{ $item['name'] }}</td>
                    </tr>
                    <tr class="border-b border-gray-100 border-dotted">
                        <td class="pl-2">{{ $item['quantity'] }} x {{ number_format($item['price'], 2) }}</td>
                        <td class="text-right">{{ number_format($item['price'] * $item['quantity'], 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <div class="border-t border-black border-dashed pt-2 space-y-1">
            <div class="flex justify-between">
                <span>Subtotal:</span>
                <span>{{ number_format($this->subtotal, 2) }}</span>
            </div>
            <div class="flex justify-between">
                <span>VAT (Incl):</span>
                <span>{{ number_format($this->vatTotal, 2) }}</span>
            </div>
            @if($discount > 0)
                <div class="flex justify-between">
                    <span>Discount:</span>
                    <span>-{{ number_format($this->discountAmount, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between text-xs font-bold">
                <span>TOTAL:</span>
                <span>{{ Number::currency($this->grandTotal) }}</span>
            </div>
        </div>

        <div class="mt-4 pt-2 border-t border-black border-dashed text-center">
            <div class="mb-2">
                {!! \Milon\Barcode\Facades\DNS1DFacade::getBarcodeHTML($lastInvoiceId ? \App\Models\Invoice::find($lastInvoiceId)->invoice_number : 'POS', 'C128', 1, 25) !!}
            </div>
            <p>Thank you for shopping!</p>
            <p>Returns accepted within 7 days.</p>
        </div>
    </div>

    <!-- Notification Toasts -->
    <div x-data="{ notifications: [] }"
        x-on:notify.window="notifications.push($event.detail[0]); setTimeout(() => notifications.shift(), 3000)"
        class="fixed bottom-4 left-4 z-[100] space-y-2">
        <template x-for="note in notifications" :key="note.message">
            <div :class="note.type === 'error' ? 'bg-red-600' : 'bg-gray-900'"
                class="text-white px-6 py-3 rounded-lg shadow-2xl flex items-center space-x-3 transition-all transform animate-bounce">
                <span x-text="note.message" class="font-bold text-sm"></span>
            </div>
        </template>
    </div>

    <!-- Bottom Action Bar (Shortcuts) -->
    <div class="bg-gray-800 p-2 flex space-x-2">
        <button wire:click="printLastReceipt"
            class="px-4 py-2 bg-gray-700 text-gray-200 rounded font-bold text-xs hover:bg-gray-600 transition flex items-center">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2h2M12 17h.01">
                </path>
            </svg>
            PRINT LAST (F4)
        </button>
        <div class="flex-1"></div>
        <div class="flex items-center space-x-4 text-xs font-bold text-gray-500 uppercase tracking-widest px-4">
            <span class="bg-gray-700 px-2 py-1 rounded text-gray-300">F1 Search</span>
            <span class="bg-gray-700 px-2 py-1 rounded text-gray-300">F2 Pay</span>
            <span class="bg-gray-700 px-2 py-1 rounded text-gray-300">F3 Hold</span>
        </div>
    </div>

    <script>
        document.addEventListener('keydown', function (e) {
            if (e.key === 'F1') {
                e.preventDefault();
                document.getElementById('pos-search-input').focus();
            }
            if (e.key === 'F2') {
                e.preventDefault();
                @this.checkout();
            }
            if (e.key === 'F3') {
                e.preventDefault();
                @this.holdSale();
            }
            if (e.key === 'F4') {
                e.preventDefault();
                @this.printLastReceipt();
            }
        });
    </script>
</div>