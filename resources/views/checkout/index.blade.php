<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Checkout') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form action="{{ route('checkout.store') }}" method="POST">
                @csrf
                <div class="flex flex-col md:flex-row gap-8">

                    <!-- Left Column: Shipping Info -->
                    <div class="md:w-2/3 space-y-6">
                        <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Shipping Information</h3>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="first_name" :value="__('First Name')" />
                                    <x-text-input id="first_name" class="block mt-1 w-full" type="text"
                                        name="first_name" :value="old('first_name', Auth::user()->name ?? '')"
                                        required />
                                    <x-input-error :messages="$errors->get('first_name')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="last_name" :value="__('Last Name')" />
                                    <x-text-input id="last_name" class="block mt-1 w-full" type="text" name="last_name"
                                        :value="old('last_name')" required />
                                    <x-input-error :messages="$errors->get('last_name')" class="mt-2" />
                                </div>
                            </div>

                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <x-input-label for="email" :value="__('Email Address')" />
                                    <x-text-input id="email" class="block mt-1 w-full" type="email" name="email"
                                        :value="old('email', Auth::user()->email ?? '')" required />
                                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="phone" :value="__('Phone Number')" />
                                    <x-text-input id="phone" class="block mt-1 w-full" type="text" name="phone"
                                        :value="old('phone')" required />
                                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                                </div>
                            </div>

                            <div class="mt-4">
                                <x-input-label for="address" :value="__('Address')" />
                                <x-text-input id="address" class="block mt-1 w-full" type="text" name="address"
                                    :value="old('address')" required />
                                <x-input-error :messages="$errors->get('address')" class="mt-2" />
                            </div>

                            <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-4">
                                <div>
                                    <x-input-label for="city" :value="__('City')" />
                                    <x-text-input id="city" class="block mt-1 w-full" type="text" name="city"
                                        :value="old('city')" required />
                                    <x-input-error :messages="$errors->get('city')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="state" :value="__('State')" />
                                    <x-text-input id="state" class="block mt-1 w-full" type="text" name="state"
                                        :value="old('state')" />
                                    <x-input-error :messages="$errors->get('state')" class="mt-2" />
                                </div>
                                <div>
                                    <x-input-label for="zip_code" :value="__('Zip Code')" />
                                    <x-text-input id="zip_code" class="block mt-1 w-full" type="text" name="zip_code"
                                        :value="old('zip_code')" required />
                                    <x-input-error :messages="$errors->get('zip_code')" class="mt-2" />
                                </div>
                            </div>

                            <div class="mt-4">
                                <x-input-label for="notes" :value="__('Order Notes (Optional)')" />
                                <textarea id="notes" name="notes" rows="3"
                                    class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">{{ old('notes') }}</textarea>
                            </div>
                        </div>

                        <div class="bg-white dark:bg-gray-800 p-6 shadow sm:rounded-lg">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Payment Method</h3>
                            <div class="space-y-4">
                                <label
                                    class="flex items-center space-x-3 p-4 border rounded-lg cursor-pointer checked:border-primary-500 checked:bg-primary-50 dark:checked:bg-primary-900/20">
                                    <input type="radio" name="payment_method" value="cash_on_delivery" checked
                                        class="text-primary-600 focus:ring-primary-500">
                                    <span class="text-gray-900 dark:text-white font-medium">Cash on Delivery</span>
                                </label>
                                <!-- Placeholders for real payment gateways -->
                                <label
                                    class="flex items-center space-x-3 p-4 border rounded-lg opacity-60 cursor-not-allowed">
                                    <input type="radio" name="payment_method" value="card" disabled
                                        class="text-primary-600 focus:ring-primary-500">
                                    <span class="text-gray-900 dark:text-white font-medium">Credit Card (Coming
                                        Soon)</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Right Column: Order Summary -->
                    <div class="md:w-1/3">
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg sticky top-24">
                            <div class="p-6">
                                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Your Order</h3>

                                <div
                                    class="divide-y divide-gray-200 dark:divide-gray-700 max-h-80 overflow-y-auto mb-4">
                                    @foreach($cart as $id => $item)
                                        <div class="py-3 flex justify-between text-sm">
                                            <div class="flex gap-2">
                                                <span
                                                    class="text-gray-500 dark:text-gray-400">{{ $item['quantity'] }}x</span>
                                                <span class="text-gray-900 dark:text-white">{{ $item['name'] }}</span>
                                            </div>
                                            <span
                                                class="text-gray-900 dark:text-white font-medium">{{ Number::currency($item['price'] * $item['quantity']) }}</span>
                                        </div>
                                    @endforeach
                                </div>

                                <div class="border-t border-gray-200 dark:border-gray-700 pt-4 space-y-2">
                                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                        <span>Subtotal</span>
                                        <span>{{ Number::currency($total) }}</span>
                                    </div>
                                    <div class="flex justify-between text-gray-600 dark:text-gray-400">
                                        <span>Shipping</span>
                                        <span>Free</span>
                                    </div>
                                    <div
                                        class="flex justify-between font-bold text-lg text-gray-900 dark:text-white pt-2">
                                        <span>Total</span>
                                        <span>{{ Number::currency($total) }}</span>
                                    </div>
                                </div>

                                <button type="submit"
                                    class="mt-6 w-full block text-center bg-gray-900 dark:bg-primary-600 text-white font-bold py-3 px-4 rounded-full hover:bg-gray-800 dark:hover:bg-primary-500 transition-colors">
                                    Place Order
                                </button>
                            </div>
                        </div>
                    </div>

                </div>
            </form>
        </div>
    </div>

    {{-- Analytics: Track Checkout Initiation --}}
    <x-analytics-events :trackInitiateCheckout="true" />
</x-app-layout>