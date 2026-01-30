<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Marketing Analytics') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Date Filter -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-6">
                <form action="{{ route('admin.marketing.index') }}" method="GET" class="flex gap-4 items-end">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Start
                            Date</label>
                        <input type="date" name="start_date" value="{{ $startDate }}"
                            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">End Date</label>
                        <input type="date" name="end_date" value="{{ $endDate }}"
                            class="rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300">
                    </div>
                    <button type="submit"
                        class="bg-primary-600 text-white px-6 py-2 rounded-md hover:bg-primary-500">Filter</button>
                </form>
            </div>

            <!-- Key Metrics -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Revenue</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        {{ Number::currency($totalRevenue) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Total Orders</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">{{ number_format($totalOrders) }}
                    </p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Avg Order Value</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        {{ Number::currency($avgOrderValue) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Conversion Rate</p>
                    <p class="text-2xl font-bold text-gray-900 dark:text-white mt-1">
                        {{ number_format($conversionRate, 2) }}%</p>
                </div>
            </div>

            <!-- Revenue Trend Chart -->
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6 mb-8">
                <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Revenue Trend (Last 14 Days)</h3>
                <canvas id="revenueChart" height="80"></canvas>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Conversion Funnel -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Conversion Funnel</h3>
                    <div class="space-y-4">
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700 dark:text-gray-300">Visitors</span>
                                <span
                                    class="font-bold text-gray-900 dark:text-white">{{ number_format($visitors) }}</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                                <div class="bg-primary-600 h-4 rounded-full" style="width: 100%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700 dark:text-gray-300">Add to Cart</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ number_format($addToCarts) }}
                                    ({{ number_format(($addToCarts / $visitors) * 100, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                                <div class="bg-green-600 h-4 rounded-full"
                                    style="width: {{ ($addToCarts / $visitors) * 100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700 dark:text-gray-300">Checkout</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ number_format($checkouts) }}
                                    ({{ number_format(($checkouts / $visitors) * 100, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                                <div class="bg-yellow-600 h-4 rounded-full"
                                    style="width: {{ ($checkouts / $visitors) * 100 }}%"></div>
                            </div>
                        </div>
                        <div>
                            <div class="flex justify-between text-sm mb-1">
                                <span class="text-gray-700 dark:text-gray-300">Purchase</span>
                                <span class="font-bold text-gray-900 dark:text-white">{{ number_format($purchases) }}
                                    ({{ number_format($conversionRate, 1) }}%)</span>
                            </div>
                            <div class="w-full bg-gray-200 dark:bg-gray-700 rounded-full h-4">
                                <div class="bg-blue-600 h-4 rounded-full" style="width: {{ $conversionRate }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Top Products -->
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <h3 class="text-lg font-bold text-gray-900 dark:text-white mb-4">Top Products</h3>
                    <canvas id="topProductsChart"></canvas>
                </div>
            </div>

            <!-- Active Campaigns -->
            @if($campaigns->count() > 0)
                <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="text-lg font-bold text-gray-900 dark:text-white">Active Campaigns</h3>
                        <a href="{{ route('admin.marketing.campaigns') }}"
                            class="text-primary-600 hover:underline text-sm">View All</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th
                                        class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Campaign</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Clicks</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Conversions</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        Revenue</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        ROI</th>
                                    <th
                                        class="px-6 py-3 text-right text-xs font-medium text-gray-500 dark:text-gray-300 uppercase">
                                        CPA</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @foreach($campaigns as $campaign)
                                    <tr>
                                        <td class="px-6 py-4 text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $campaign->name }}</td>
                                        <td class="px-6 py-4 text-sm text-right text-gray-500 dark:text-gray-400">
                                            {{ number_format($campaign->clicks) }}</td>
                                        <td class="px-6 py-4 text-sm text-right text-gray-500 dark:text-gray-400">
                                            {{ number_format($campaign->conversions) }}</td>
                                        <td class="px-6 py-4 text-sm text-right font-medium text-gray-900 dark:text-white">
                                            {{ Number::currency($campaign->revenue) }}</td>
                                        <td
                                            class="px-6 py-4 text-sm text-right {{ $campaign->roi >= 0 ? 'text-green-600' : 'text-red-600' }}">
                                            {{ number_format($campaign->roi, 1) }}%</td>
                                        <td class="px-6 py-4 text-sm text-right text-gray-500 dark:text-gray-400">
                                            {{ Number::currency($campaign->cpa) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Revenue Trend Chart
        const revenueCtx = document.getElementById('revenueChart').getContext('2d');
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: {!! json_encode(array_keys($revenueData->toArray())) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode(array_values($revenueData->toArray())) !!},
                    borderColor: 'rgb(59, 130, 246)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    tension: 0.4,
                    fill: true
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });

        // Top Products Chart
        const productsCtx = document.getElementById('topProductsChart').getContext('2d');
        new Chart(productsCtx, {
            type: 'bar',
            data: {
                labels: {!! json_encode($topProducts->pluck('name')->toArray()) !!},
                datasets: [{
                    label: 'Revenue',
                    data: {!! json_encode($topProducts->pluck('revenue')->toArray()) !!},
                    backgroundColor: 'rgb(34, 197, 94)',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true,
                plugins: {
                    legend: { display: false }
                },
                scales: {
                    y: { beginAtZero: true }
                }
            }
        });
    </script>
</x-app-layout>