<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Order Details') }} #{{ $order->order_number }}
            </h2>
            <a href="{{ route('orders.index') }}" class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                &larr; Back to Orders
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                
                <!-- Main Content: Items -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-bold mb-4">Order Items</h3>
                            <div class="divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($order->items as $item)
                                    <div class="py-4 flex gap-4">
                                        <div class="w-20 h-20 flex-shrink-0 bg-gray-100 dark:bg-gray-900 rounded-md overflow-hidden">
                                            @if($item->product && $item->product->image_url)
                                                <img src="{{ $item->product->image_url }}" alt="{{ $item->product->name }}" class="w-full h-full object-cover">
                                            @else
                                                <div class="w-full h-full flex items-center justify-center text-xs text-gray-500">No Image</div>
                                            @endif
                                        </div>
                                        <div class="flex-grow">
                                            <h4 class="font-medium text-gray-900 dark:text-white">{{ optional($item->product)->name ?? 'Product Unavailable' }}</h4>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Unit Price: {{ Number::currency($item->price) }}</p>
                                            <p class="text-sm text-gray-500 dark:text-gray-400">Qty: {{ $item->quantity }}</p>
                                        </div>
                                        <div class="font-bold text-gray-900 dark:text-white">
                                            {{ Number::currency($item->price * $item->quantity) }}
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar: Summary & Info -->
                <div class="space-y-6">
                    <!-- Order Summary -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-bold mb-4">Summary</h3>
                            <div class="flex justify-between mb-2 text-sm text-gray-600 dark:text-gray-400">
                                <span>Subtotal</span>
                                <span>{{ Number::currency($order->grand_total) }}</span>
                            </div>
                            <div class="flex justify-between mb-2 text-sm text-gray-600 dark:text-gray-400">
                                <span>Shipping</span>
                                <span>Free</span>
                            </div>
                            <div class="border-t border-gray-200 dark:border-gray-700 pt-2 mt-2 flex justify-between font-bold text-lg">
                                <span>Total</span>
                                <span>{{ Number::currency($order->grand_total) }}</span>
                            </div>
                            
                            <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                <div class="flex justify-between items-center">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Status</span>
                                    <span class="capitalize font-bold">{{ $order->status }}</span>
                                </div>
                                <div class="flex justify-between items-center mt-2">
                                    <span class="text-sm text-gray-600 dark:text-gray-400">Payment</span>
                                    <span class="capitalize font-bold">{{ str_replace('_', ' ', $order->payment_method) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Shipping Address -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-bold mb-4">Shipping Address</h3>
                            <address class="not-italic text-sm text-gray-600 dark:text-gray-400">
                                {{ $order->first_name }} {{ $order->last_name }}<br>
                                {{ $order->address }}<br>
                                {{ $order->city }}, {{ $order->state }} {{ $order->zip_code }}<br>
                                {{ $order->phone }}
                            </address>
                            @if($order->notes)
                                <div class="mt-4 pt-4 border-t border-gray-200 dark:border-gray-700">
                                    <h4 class="font-bold text-xs uppercase text-gray-500 mb-1">Notes</h4>
                                    <p class="text-sm">{{ $order->notes }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    {{-- Analytics: Track Purchase --}}
    <x-analytics-events :trackPurchase="true" :order="$order" />
</x-app-layout>
