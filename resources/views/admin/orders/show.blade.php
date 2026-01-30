<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Manage Order') }} #{{ $order->order_number }}
            </h2>
            <a href="{{ route('admin.orders.index') }}"
                class="text-sm text-gray-600 hover:text-gray-900 dark:text-gray-400 dark:hover:text-gray-100">
                &larr; Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                <!-- Left Column: Order Information & Status -->
                <div class="md:col-span-2 space-y-6">

                    <!-- Items -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-bold mb-4">Order Items</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th
                                                class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                Product</th>
                                            <th
                                                class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                Price</th>
                                            <th
                                                class="text-left text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                Qty</th>
                                            <th
                                                class="text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                VAT</th>
                                            <th
                                                class="text-right text-xs font-medium text-gray-500 dark:text-gray-400 uppercase">
                                                Total</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($order->items as $item)
                                            <tr>
                                                <td class="py-3">
                                                    <div class="flex items-center">
                                                        <div class="h-10 w-10 flex-shrink-0">
                                                            @if($item->product && $item->product->image_url)
                                                                <img class="h-10 w-10 rounded-full object-cover"
                                                                    src="{{ $item->product->image_url }}" alt="">
                                                            @else
                                                                <div
                                                                    class="h-10 w-10 rounded-full bg-gray-200 dark:bg-gray-700">
                                                                </div>
                                                            @endif
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ optional($item->product)->name ?? 'Product Unavailable' }}
                                                            </div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td class="py-3 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ Number::currency($item->price) }}
                                                </td>
                                                <td class="py-3 text-sm text-gray-500 dark:text-gray-400">
                                                    {{ $item->quantity }}
                                                </td>
                                                <td class="py-3 text-sm text-right text-gray-500 dark:text-gray-400">
                                                    {{ Number::currency($item->vat_amount) }}
                                                </td>
                                                <td
                                                    class="py-3 text-sm text-right font-medium text-gray-900 dark:text-white">
                                                    {{ Number::currency($item->price * $item->quantity) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Payment & Shipping -->
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-lg font-bold mb-4">Shipping Address</h3>
                                    <address class="not-italic text-sm text-gray-600 dark:text-gray-400">
                                        {{ $order->first_name }} {{ $order->last_name }}<br>
                                        {{ $order->address }}<br>
                                        {{ $order->city }}, {{ $order->state }} {{ $order->zip_code }}<br>
                                        {{ $order->phone }}<br>
                                        {{ $order->email }}
                                    </address>
                                </div>
                                <div>
                                    <h3 class="text-lg font-bold mb-4">Details</h3>
                                    <div class="space-y-2 text-sm">
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Payment Method:</span>
                                            <span
                                                class="font-medium capitalize">{{ str_replace('_', ' ', $order->payment_method) }}</span>
                                        </div>
                                        <div class="flex justify-between">
                                            <span class="text-gray-500">Is Paid:</span>
                                            <span
                                                class="font-medium {{ $order->is_paid ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $order->is_paid ? 'Yes' : 'No' }}
                                            </span>
                                        </div>
                                        @if($order->notes)
                                            <div
                                                class="mt-4 p-3 bg-gray-50 dark:bg-gray-700 rounded text-gray-600 dark:text-gray-300">
                                                <span class="font-bold text-xs uppercase block mb-1">Customer Notes:</span>
                                                {{ $order->notes }}
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Actions -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg sticky top-24">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            <h3 class="text-lg font-bold mb-4">Update Status</h3>

                            <form action="{{ route('admin.orders.update', $order) }}" method="POST">
                                @csrf
                                @method('PATCH')

                                <div class="mb-4">
                                    <label for="status"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Order
                                        Status</label>
                                    <select name="status" id="status"
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                                        @foreach(['pending', 'processing', 'completed', 'cancelled', 'declined'] as $status)
                                            <option value="{{ $status }}" {{ $order->status === $status ? 'selected' : '' }}>
                                                {{ ucfirst($status) }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                    Update Status
                                </button>
                            </form>

                            <div class="mt-6 border-t border-gray-200 dark:border-gray-700 pt-4">
                                <h4 class="font-bold text-sm mb-2">Order Totals</h4>
                                <div class="flex justify-between mb-1 text-sm">
                                    <span class="text-gray-500">Net Subtotal</span>
                                    <span>{{ Number::currency($order->grand_total - $order->items->sum('vat_amount')) }}</span>
                                </div>
                                <div class="flex justify-between mb-1 text-sm">
                                    <span class="text-gray-500">Total VAT</span>
                                    <span>{{ Number::currency($order->items->sum('vat_amount')) }}</span>
                                </div>
                                <div
                                    class="flex justify-between font-bold text-lg mt-2 pt-2 border-t border-gray-100 dark:border-gray-700">
                                    <span>Grand Total</span>
                                    <span>{{ Number::currency($order->grand_total) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>