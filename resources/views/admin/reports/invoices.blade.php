<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Sales Invoices Report') }}
            </h2>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center px-4 py-2 bg-orange-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-orange-500">
                    Download Invoices
                    <svg class="ml-2 -mr-0.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-1">
                        <a href="{{ route('admin.reports.invoices.download', ['format' => 'pdf', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">PDF Document</a>
                        <a href="{{ route('admin.reports.invoices.download', ['format' => 'csv', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">CSV Spreadsheet</a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <form action="{{ route('admin.reports.invoices') }}" method="GET" class="flex gap-4 items-end">
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
                        class="bg-orange-600 text-white px-6 py-2 rounded-md hover:bg-orange-500">Filter</button>
                </form>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-orange-500">
                    <p class="text-xs font-bold text-gray-400 uppercase">Total Sales Revenue</p>
                    <p class="text-2xl font-bold text-orange-600 mt-1">{{ Number::currency($totalSales) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm border-l-4 border-indigo-500">
                    <p class="text-xs font-bold text-gray-400 uppercase">Total VAT Collected</p>
                    <p class="text-2xl font-bold text-indigo-600 mt-1">{{ Number::currency($totalVat) }}</p>
                </div>
            </div>

            <div
                class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg overflow-hidden border border-gray-100 dark:border-gray-700">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead class="bg-gray-50 dark:bg-gray-700">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Inv #</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Customer</th>
                                <th class="px-6 py-3 text-left text-xs font-bold text-gray-500 uppercase">Date</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">VAT</th>
                                <th class="px-6 py-3 text-right text-xs font-bold text-gray-500 uppercase">Total</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                            @forelse($invoices as $inv)
                                <tr>
                                    <td class="px-6 py-4 text-sm font-bold">{{ $inv->invoice_number }}</td>
                                    <td class="px-6 py-4 text-sm text-gray-600 dark:text-gray-400">
                                        {{ $inv->customer->name ?? 'Walk-in' }}</td>
                                    <td class="px-6 py-4 text-xs text-gray-500">{{ $inv->issued_at->format('Y-m-d') }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-mono text-indigo-600 font-bold">
                                        {{ Number::currency($inv->total_vat_amount) }}</td>
                                    <td class="px-6 py-4 text-right text-sm font-bold">
                                        {{ Number::currency($inv->total_amount) }}</td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="p-6 text-center text-gray-400 italic">No invoices found for this
                                        period</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>