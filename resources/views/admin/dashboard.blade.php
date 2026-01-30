<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Admin Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Today's Sales</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                {{ Number::currency($todaySales) }}
                            </p>
                            <p class="text-xs text-gray-500 mt-1">{{ $todayOrders }} orders</p>
                        </div>
                        <div class="p-3 bg-primary-100 dark:bg-primary-900 rounded-full">
                            <svg class="w-8 h-8 text-primary-600 dark:text-primary-400" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">This Week</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                {{ Number::currency($weekSales) }}
                            </p>
                        </div>
                        <div class="p-3 bg-green-100 dark:bg-green-900 rounded-full">
                            <svg class="w-8 h-8 text-green-600 dark:text-green-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">This Month</p>
                            <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                                {{ Number::currency($monthSales) }}
                            </p>
                        </div>
                        <div class="p-3 bg-purple-100 dark:bg-purple-900 rounded-full">
                            <svg class="w-8 h-8 text-purple-600 dark:text-purple-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z" />
                            </svg>
                        </div>
                    </div>
                </div>

                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center justify-between">
                        <div>
                            <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Supplier Liability</p>
                            <p class="text-2xl font-bold text-red-600 dark:text-red-400 mt-1">
                                {{ Number::currency($totalLiability) }}
                            </p>
                        </div>
                        <div class="p-3 bg-red-100 dark:bg-red-900 rounded-full">
                            <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Revenue Chart -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Revenue (Last 7 Days)</h3>
                <div class="h-64 flex items-end justify-between gap-2">
                    @foreach($chartData as $date => $amount)
                        @php
                            $maxAmount = $chartData->max();
                            $height = $maxAmount > 0 ? ($amount / $maxAmount) * 100 : 0;
                        @endphp
                        <div class="flex-1 flex flex-col items-center">
                            <div class="w-full bg-primary-600 hover:bg-primary-500 transition-all rounded-t relative group cursor-pointer"
                                style="height: {{ $height }}%;">
                                <div
                                    class="absolute bottom-full left-1/2 transform -translate-x-1/2 mb-2 px-2 py-1 bg-gray-900 text-white text-xs rounded opacity-0 group-hover:opacity-100 transition-opacity whitespace-nowrap">
                                    {{ Number::currency($amount) }}
                                </div>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">{{ \Carbon\Carbon::parse($date)->format('M d') }}</p>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <!-- Low Stock Alerts -->
                @if($lowStockProducts->count() > 0 || $outOfStockProducts > 0)
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                        <div class="flex items-center justify-between mb-4">
                            <h3 class="text-lg font-bold text-gray-900 dark:text-white">Inventory Alerts</h3>
                            @if($outOfStockProducts > 0)
                                <span
                                    class="px-3 py-1 bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300 rounded-full text-xs font-bold">
                                    {{ $outOfStockProducts }} Out of Stock
                                </span>
                            @endif
                        </div>
                        <div class="space-y-3 max-h-64 overflow-y-auto">
                            @foreach($lowStockProducts as $product)
                                <div
                                    class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                    <div>
                                        <a href="{{ route('admin.products.edit', $product) }}"
                                            class="text-sm font-medium text-gray-900 dark:text-white hover:text-primary-600">
                                            {{ $product->name }}
                                        </a>
                                        <p class="text-xs text-gray-500">SKU: {{ $product->sku ?? 'N/A' }}</p>
                                    </div>
                                    <span
                                        class="px-2 py-1 {{ $product->stock_quantity == 0 ? 'bg-red-100 text-red-800 dark:bg-red-900/50 dark:text-red-300' : 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900/50 dark:text-yellow-300' }} rounded-full text-xs font-bold">
                                        {{ $product->stock_quantity }} left
                                    </span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Top Products -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Top Products</h3>
                    <div class="space-y-3">
                        @foreach($topProducts as $product)
                            <div
                                class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                <span class="text-sm text-gray-700 dark:text-gray-300">{{ $product->name }}</span>
                                <span
                                    class="text-sm font-bold text-gray-900 dark:text-white">{{ Number::currency($product->revenue) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>

                <!-- Recent Orders -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Recent Orders</h3>
                    <div class="space-y-3">
                        @foreach($recentOrders as $order)
                            <div
                                class="flex justify-between items-center pb-3 border-b border-gray-200 dark:border-gray-700 last:border-0">
                                <div>
                                    <a href="{{ route('admin.orders.show', $order) }}"
                                        class="text-sm font-medium text-primary-600 hover:underline">
                                        #{{ $order->order_number }}
                                    </a>
                                    <p class="text-xs text-gray-500">{{ $order->created_at->diffForHumans() }}</p>
                                </div>
                                <span
                                    class="text-sm font-bold text-gray-900 dark:text-white">{{ Number::currency($order->grand_total) }}</span>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>