<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Shopping Cart') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row gap-8">

                <!-- Cart Items -->
                <div class="md:w-3/4">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                            @if(count($cart) > 0)
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr>
                                            <th
                                                class="py-4 px-6 bg-gray-50 dark:bg-gray-700 font-bold uppercase text-sm text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600">
                                                Product</th>
                                            <th
                                                class="py-4 px-6 bg-gray-50 dark:bg-gray-700 font-bold uppercase text-sm text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600">
                                                Price</th>
                                            <th
                                                class="py-4 px-6 bg-gray-50 dark:bg-gray-700 font-bold uppercase text-sm text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600">
                                                Quantity</th>
                                            <th
                                                class="py-4 px-6 bg-gray-50 dark:bg-gray-700 font-bold uppercase text-sm text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600">
                                                Total</th>
                                            <th
                                                class="py-4 px-6 bg-gray-50 dark:bg-gray-700 font-bold uppercase text-sm text-gray-600 dark:text-gray-300 border-b border-gray-200 dark:border-gray-600">
                                            </th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach($cart as $id => $details)
                                            <tr class="hover:bg-gray-50 dark:hover:bg-gray-700">
                                                <td class="py-4 px-6 border-b border-gray-200 dark:border-gray-600">
                                                    <div class="flex items-center">
                                                        <div class="flex-shrink-0 h-16 w-16">
                                                            <img src="{{ $details['image_url'] }}" alt="{{ $details['name'] }}"
                                                                class="h-16 w-16 object-cover rounded">
                                                        </div>
                                                        <div class="ml-4">
                                                            <div class="text-sm font-medium text-gray-900 dark:text-white">
                                                                {{ $details['name'] }}</div>
                                                        </div>
                                                    </div>
                                                </td>
                                                <td
                                                    class="py-4 px-6 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-gray-300">
                                                    {{ Number::currency($details['price']) }}
                                                </td>
                                                <td class="py-4 px-6 border-b border-gray-200 dark:border-gray-600">
                                                    <form action="{{ route('cart.update', $id) }}" method="POST"
                                                        class="flex items-center">
                                                        @csrf
                                                        @method('PATCH')
                                                        <input type="number" name="quantity" value="{{ $details['quantity'] }}"
                                                            min="1"
                                                            class="w-16 text-center border-gray-300 dark:border-gray-700 rounded-md shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 dark:bg-gray-900 dark:text-gray-300">
                                                        <button type="submit"
                                                            class="ml-2 text-indigo-600 hover:text-indigo-900 dark:text-indigo-400 dark:hover:text-indigo-200 text-sm">Update</button>
                                                    </form>
                                                </td>
                                                <td
                                                    class="py-4 px-6 border-b border-gray-200 dark:border-gray-600 text-gray-900 dark:text-white font-semibold">
                                                    {{ Number::currency($details['price'] * $details['quantity']) }}
                                                </td>
                                                <td class="py-4 px-6 border-b border-gray-200 dark:border-gray-600">
                                                    <form action="{{ route('cart.remove', $id) }}" method="POST">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit"
                                                            class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-200">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                                viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                                    stroke-width="2"
                                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                                </path>
                                                            </svg>
                                                        </button>
                                                    </form>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            @else
                                <div class="text-center py-10">
                                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100">Your cart is empty</h3>
                                    <p class="text-gray-500 dark:text-gray-400 mt-2">Looks like you haven't added anything
                                        to your cart yet.</p>
                                    <a href="{{ route('home') }}"
                                        class="mt-6 inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 focus:bg-primary-700 active:bg-primary-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                                        Start Shopping
                                    </a>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="md:w-1/4">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg sticky top-24">
                        <div class="p-6">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Order Summary</h3>

                            <div class="flex justify-between mb-2">
                                <span>Subtotal</span>
                                <span>{{ Number::currency($total) }}</span>
                            </div>

                            <!-- Coupon Form -->
                            <div class="mb-4 p-3 bg-gray-50 dark:bg-gray-700 rounded">
                                @php
                                    $appliedCoupon = app(\App\Services\CartService::class)->getAppliedCoupon();
                                @endphp
                                
                                @if($appliedCoupon)
                                    <div class="flex justify-between items-center mb-2 text-green-600 dark:text-green-400">
                                        <span class="font-medium">Coupon: {{ $appliedCoupon['code'] }}</span>
                                        <form action="{{ route('coupon.remove') }}" method="POST" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-xs text-red-600 hover:underline">Remove</button>
                                        </form>
                                    </div>
                                    <div class="flex justify-between text-green-600 dark:text-green-400">
                                        <span>Discount</span>
                                        <span>-{{ Number::currency($appliedCoupon['discount']) }}</span>
                                    </div>
                                @else
                                    <form action="{{ route('coupon.apply') }}" method="POST" class="flex gap-2">
                                        @csrf
                                        <input type="text" name="coupon_code" placeholder="Enter coupon code" class="flex-1 rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 text-sm">
                                        <button type="submit" class="bg-gray-900 dark:bg-primary-600 text-white px-4 py-2 rounded text-sm hover:bg-gray-800">Apply</button>
                                    </form>
                                @endif
                            </div>

                            <div class="flex justify-between mb-2 text-gray-600 dark:text-gray-400">
                                <span>Tax</span>
                                <span>$0.00</span>
                            </div>
                            <div class="flex justify-between mb-4 text-gray-600 dark:text-gray-400">
                                <span>Shipping</span>
                                <span>Free</span>
                            </div>

                            <div
                                class="border-t border-gray-200 dark:border-gray-700 pt-4 flex justify-between font-bold text-lg text-gray-900 dark:text-white mb-6">
                                <span>Total</span>
                                <span>{{ Number::currency($appliedCoupon ? $total - $appliedCoupon['discount'] : $total) }}</span>
                            </div>

                            @if(count($cart) > 0)
                                <a href="{{ route('checkout.index') }}"
                                    class="w-full block text-center bg-gray-900 dark:bg-primary-600 text-white font-bold py-3 px-4 rounded-full hover:bg-gray-800 dark:hover:bg-primary-500 transition-colors">
                                    Proceed to Checkout
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>