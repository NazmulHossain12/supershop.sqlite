<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Accounting & Financial Reports') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Financial Overview Cards -->
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4 mb-8">
                <!-- Total Assets -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-xs font-bold uppercase text-gray-400 mb-1">Total Assets</p>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-white">{{ Number::currency($totalAssets) }}</h3>
                </div>

                <!-- Total Liabilities -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-xs font-bold uppercase text-gray-400 mb-1">Total Liabilities</p>
                    <h3 class="text-xl font-bold text-red-600 dark:text-red-400">{{ Number::currency($totalLiabilities) }}</h3>
                </div>

                <!-- Total Revenue -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-xs font-bold uppercase text-gray-400 mb-1">Total Revenue</p>
                    <h3 class="text-xl font-bold text-green-600 dark:text-green-400">{{ Number::currency($totalRevenue) }}</h3>
                </div>

                <!-- Total Expenses -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-xs font-bold uppercase text-gray-400 mb-1">Total Expenses</p>
                    <h3 class="text-xl font-bold text-orange-600 dark:text-orange-400">{{ Number::currency($totalExpenses) }}</h3>
                </div>

                <!-- Net Profit -->
                <div class="bg-white dark:bg-gray-800 p-4 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
                    <p class="text-xs font-bold uppercase text-gray-400 mb-1">Net Profit (P&L)</p>
                    <h3 class="text-xl font-bold {{ $netProfit >= 0 ? 'text-primary-600' : 'text-red-700' }}">
                        {{ Number::currency($netProfit) }}
                    </h3>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Chart of Accounts Summary -->
                <div class="lg:col-span-1">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-bold text-gray-900 dark:text-white">Chart of Accounts</h3>
                        </div>
                        <div class="p-4 bg-gray-50 dark:bg-gray-700/50">
                            <h4 class="text-xs font-bold uppercase text-gray-500 mb-2">Assets</h4>
                            <table class="min-w-full mb-6">
                                <tbody>
                                    @foreach($assets as $account)
                                        <tr class="border-b border-gray-100 dark:border-gray-700 last:border-0">
                                            <td class="py-2 text-sm text-gray-600 dark:text-gray-400">{{ $account->name }}</td>
                                            <td class="py-2 text-sm font-bold text-right text-gray-900 dark:text-white">{{ Number::currency($account->balance) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <h4 class="text-xs font-bold uppercase text-gray-500 mb-2">Liabilities</h4>
                            <table class="min-w-full mb-6">
                                <tbody>
                                    @foreach($liabilities as $account)
                                        <tr class="border-b border-gray-100 dark:border-gray-700 last:border-0">
                                            <td class="py-2 text-sm text-gray-600 dark:text-gray-400">{{ $account->name }}</td>
                                            <td class="py-2 text-sm font-bold text-right text-gray-900 dark:text-white">{{ Number::currency($account->balance) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>

                            <h4 class="text-xs font-bold uppercase text-gray-500 mb-2">Equity</h4>
                            <table class="min-w-full">
                                <tbody>
                                    @foreach($equity as $account)
                                        <tr class="border-b border-gray-100 dark:border-gray-700 last:border-0">
                                            <td class="py-2 text-sm text-gray-600 dark:text-gray-400">{{ $account->name }}</td>
                                            <td class="py-2 text-sm font-bold text-right text-gray-900 dark:text-white">{{ Number::currency($account->balance) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent Ledger Entries -->
                <div class="lg:col-span-2">
                    <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-bold text-gray-900 dark:text-white">Recent Ledger Transactions</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Description</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Debit</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Credit</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($recentTransactions as $tx)
                                        @foreach($tx->ledgers as $ledger)
                                            <tr>
                                                <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">{{ $tx->transaction_date->format('M d, H:i') }}</td>
                                                <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                                    <div class="font-medium">{{ $ledger->account->name }}</div>
                                                    <div class="text-xs text-gray-500">{{ $ledger->entry_description ?? $tx->description }}</div>
                                                </td>
                                                <td class="px-6 py-4 text-right text-sm text-gray-900 dark:text-white">
                                                    {{ $ledger->debit > 0 ? Number::currency($ledger->debit) : '-' }}
                                                </td>
                                                <td class="px-6 py-4 text-right text-sm text-gray-900 dark:text-white">
                                                    {{ $ledger->credit > 0 ? Number::currency($ledger->credit) : '-' }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>