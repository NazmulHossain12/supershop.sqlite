<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('VAT Report') }}
            </h2>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500">
                    Download VAT Report
                    <svg class="ml-2 -mr-0.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-1">
                        <a href="{{ route('admin.reports.vat.download', ['format' => 'pdf', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">PDF Document</a>
                        <a href="{{ route('admin.reports.vat.download', ['format' => 'csv', 'start_date' => $startDate, 'end_date' => $endDate]) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">CSV Spreadsheet</a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- Date Filter -->
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg p-6 mb-6">
                <form action="{{ route('admin.reports.vat') }}" method="GET" class="flex gap-4 items-end">
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

            <!-- VAT Summary -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Output VAT (Sales)</p>
                    <p class="text-2xl font-bold text-red-600 mt-1">{{ Number::currency($outputVat) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Input VAT (Purchases)</p>
                    <p class="text-2xl font-bold text-green-600 mt-1">{{ Number::currency($inputVat) }}</p>
                </div>
                <div class="bg-white dark:bg-gray-800 p-6 rounded-lg shadow-sm">
                    <p class="text-sm font-medium text-gray-600 dark:text-gray-400">Net VAT Position</p>
                    <p class="text-2xl font-bold {{ $netVat >= 0 ? 'text-red-600' : 'text-green-600' }} mt-1">
                        {{ $netVat >= 0 ? 'Payable: ' : 'Credit: ' }}{{ Number::currency(abs($netVat)) }}
                    </p>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Sales VAT Details -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4">Sales VAT Breakdown</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ref
                                            #</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">VAT
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($salesDetails as $invoice)
                                        <tr>
                                            <td class="px-4 py-3 text-sm">{{ $invoice->issued_at->format('M d, Y') }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $invoice->invoice_number }}</td>
                                            <td class="px-4 py-3 text-right text-sm font-medium text-red-600">
                                                {{ Number::currency($invoice->total_vat_amount) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-4 text-center text-gray-500">No sales records
                                                found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Purchase VAT Details -->
                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <h3 class="text-lg font-bold mb-4">Purchase VAT Breakdown</h3>
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                <thead>
                                    <tr>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date
                                        </th>
                                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ref
                                            #</th>
                                        <th class="px-4 py-3 text-right text-xs font-medium text-gray-500 uppercase">VAT
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                    @forelse($purchaseDetails as $po)
                                        <tr>
                                            <td class="px-4 py-3 text-sm">{{ $po->updated_at->format('M d, Y') }}</td>
                                            <td class="px-4 py-3 text-sm">{{ $po->reference_no }}</td>
                                            <td class="px-4 py-3 text-right text-sm font-medium text-green-600">
                                                {{ Number::currency($po->total_vat_amount) }}
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-4 text-center text-gray-500">No purchase records
                                                found</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>