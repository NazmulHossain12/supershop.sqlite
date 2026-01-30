<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('VAT (Value Added Tax) Report') }}
            </h2>
            <a href="{{ route('admin.accounting.index') }}" class="text-sm text-primary-600 hover:underline">Back to
                Accounting</a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Summary Card -->
            <div
                class="bg-white dark:bg-gray-800 p-8 rounded-2xl shadow-sm border border-gray-100 dark:border-gray-700 mb-8 flex flex-col md:flex-row justify-between items-center gap-6">
                <div>
                    <h3 class="text-gray-500 text-sm uppercase tracking-wider font-bold mb-1">Total Sales Tax Payable
                    </h3>
                    <p class="text-4xl font-extrabold text-gray-900 dark:text-white">
                        {{ Number::currency($totalVatCollected) }}</p>
                </div>
                <div
                    class="p-4 bg-primary-50 dark:bg-primary-900/20 rounded-xl border border-primary-100 dark:border-primary-800">
                    <p class="text-xs text-primary-700 dark:text-primary-300">Liability Account: <strong>2200 - Sales
                            Tax Payable</strong></p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                <!-- Monthly Breakdown -->
                <div class="lg:col-span-1">
                    <div
                        class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-bold text-gray-900 dark:text-white">Monthly Breakdown</h3>
                        </div>
                        <div class="p-0">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">
                                            Period</th>
                                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase">VAT
                                            Amount</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-100 dark:divide-gray-700">
                                    @foreach($vatByMonth as $month)
                                        <tr>
                                            <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                                {{ DateTime::createFromFormat('!m', $month->month)->format('F') }}
                                                {{ $month->year }}
                                            </td>
                                            <td
                                                class="px-6 py-4 text-sm font-bold text-right text-gray-900 dark:text-white">
                                                {{ Number::currency($month->total) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Recent VAT Entries -->
                <div class="lg:col-span-2">
                    <div
                        class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden border border-gray-100 dark:border-gray-700">
                        <div class="p-6 border-b border-gray-100 dark:border-gray-700">
                            <h3 class="font-bold text-gray-900 dark:text-white">Recent Tax Audit Trail</h3>
                        </div>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead class="bg-gray-50 dark:bg-gray-700">
                                    <tr>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Date</th>
                                        <th
                                            class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            Transaction</th>
                                        <th
                                            class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                            VAT Credit</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                    @foreach($recentVatTransfers as $ledger)
                                        <tr>
                                            <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-500">
                                                {{ $ledger->created_at->format('M d, Y H:i') }}
                                            </td>
                                            <td class="px-6 py-4 text-sm text-gray-900 dark:text-white">
                                                <div class="font-medium">Order
                                                    #{{ $ledger->transaction->order->order_number ?? 'N/A' }}</div>
                                                <div class="text-xs text-gray-500">{{ $ledger->entry_description }}</div>
                                            </td>
                                            <td class="px-6 py-4 text-right text-sm font-bold text-green-600">
                                                +{{ Number::currency($ledger->credit) }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="p-4 border-t border-gray-100 dark:border-gray-700">
                            {{ $recentVatTransfers->links() }}
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>