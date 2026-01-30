<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Cashflow Statement') }}
            </h2>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center px-4 py-2 bg-emerald-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-emerald-500">
                    Download Report
                    <svg class="ml-2 -mr-0.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-1">
                        <a href="{{ route('admin.reports.cashflow.download', ['format' => 'pdf', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">PDF Document</a>
                        <a href="{{ route('admin.reports.cashflow.download', ['format' => 'csv', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">CSV Spreadsheet</a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <form action="{{ route('admin.reports.cashflow') }}" method="GET" class="flex gap-4 items-end">
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
                        class="bg-emerald-600 text-white px-6 py-2 rounded-md hover:bg-emerald-500">Filter</button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-emerald-500">
                    <p class="text-xs font-bold text-gray-400 uppercase">Total Inflow</p>
                    <p class="text-2xl font-bold text-emerald-600 mt-1">{{ Number::currency($totalInflow) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-rose-500">
                    <p class="text-xs font-bold text-gray-400 uppercase">Total Outflow</p>
                    <p class="text-2xl font-bold text-rose-600 mt-1">{{ Number::currency($totalOutflow) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-blue-500">
                    <p class="text-xs font-bold text-gray-400 uppercase">Net Cashflow</p>
                    <p class="text-2xl font-bold {{ $netCashflow >= 0 ? 'text-blue-600' : 'text-rose-600' }} mt-1">
                        {{ Number::currency($netCashflow) }}</p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Inflows -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-emerald-50 dark:bg-emerald-900/20">
                        <h3 class="font-bold text-emerald-800 dark:text-emerald-300">Detailed Inflows</h3>
                    </div>
                    <div class="p-0 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900/50">
                                    <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Account</th>
                                    <th class="px-4 py-2 text-right text-xs text-gray-500 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 italic">
                                @forelse($inflows as $in)
                                    <tr>
                                        <td class="px-4 py-2 text-xs">
                                            {{ $in->transaction->transaction_date->format('M d') }}</td>
                                        <td class="px-4 py-2 text-xs">{{ $in->account->name }}</td>
                                        <td class="px-4 py-2 text-xs text-right font-bold text-emerald-600">
                                            {{ Number::currency($in->debit) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-4 text-center text-gray-400 italic">No inflows this period
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Outflows -->
                <div class="bg-white dark:bg-gray-800 shadow-sm rounded-xl overflow-hidden">
                    <div class="p-6 border-b border-gray-100 dark:border-gray-700 bg-rose-50 dark:bg-rose-900/20">
                        <h3 class="font-bold text-rose-800 dark:text-rose-300">Detailed Outflows</h3>
                    </div>
                    <div class="p-0 overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead>
                                <tr class="bg-gray-50 dark:bg-gray-900/50">
                                    <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Date</th>
                                    <th class="px-4 py-2 text-left text-xs text-gray-500 uppercase">Account</th>
                                    <th class="px-4 py-2 text-right text-xs text-gray-500 uppercase">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100 dark:divide-gray-800 italic">
                                @forelse($outflows as $out)
                                    <tr>
                                        <td class="px-4 py-2 text-xs">
                                            {{ $out->transaction->transaction_date->format('M d') }}</td>
                                        <td class="px-4 py-2 text-xs">{{ $out->account->name }}</td>
                                        <td class="px-4 py-2 text-xs text-right font-bold text-rose-600">
                                            {{ Number::currency($out->credit) }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="p-4 text-center text-gray-400 italic">No outflows this period
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>