<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Balance Sheet') }}
            </h2>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-500">
                    Download Report
                    <svg class="ml-2 -mr-0.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-1">
                        <a href="{{ route('admin.reports.balance-sheet.download', ['format' => 'pdf']) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">PDF Document</a>
                        <a href="{{ route('admin.reports.balance-sheet.download', ['format' => 'csv']) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">CSV Spreadsheet</a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg p-6">

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <!-- Assets -->
                    <div>
                        <h3 class="text-lg font-bold border-b-2 border-green-500 pb-2 mb-4">Assets</h3>
                        <table class="w-full text-sm">
                            <tbody>
                                @foreach($assets as $account)
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="py-2 text-gray-600 dark:text-gray-400">{{ $account->name }}</td>
                                        <td class="py-2 text-right font-bold">{{ Number::currency($account->balance) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50 dark:bg-gray-700/50 font-bold">
                                    <td class="py-2 px-2 text-gray-700 dark:text-white">Total Assets</td>
                                    <td class="py-2 px-2 text-right text-green-600">{{ Number::currency($totalAssets) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Liabilities -->
                    <div>
                        <h3 class="text-lg font-bold border-b-2 border-red-500 pb-2 mb-4">Liabilities</h3>
                        <table class="w-full text-sm">
                            <tbody>
                                @foreach($liabilities as $account)
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="py-2 text-gray-600 dark:text-gray-400">{{ $account->name }}</td>
                                        <td class="py-2 text-right font-bold">{{ Number::currency($account->balance) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50 dark:bg-gray-700/50 font-bold">
                                    <td class="py-2 px-2 text-gray-700 dark:text-white">Total Liabilities</td>
                                    <td class="py-2 px-2 text-right text-red-600">
                                        {{ Number::currency($totalLiabilities) }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <!-- Equity -->
                    <div>
                        <h3 class="text-lg font-bold border-b-2 border-blue-500 pb-2 mb-4">Equity</h3>
                        <table class="w-full text-sm">
                            <tbody>
                                @foreach($equity as $account)
                                    <tr class="border-b border-gray-100 dark:border-gray-700">
                                        <td class="py-2 text-gray-600 dark:text-gray-400">{{ $account->name }}</td>
                                        <td class="py-2 text-right font-bold">{{ Number::currency($account->balance) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="bg-gray-50 dark:bg-gray-700/50 font-bold">
                                    <td class="py-2 px-2 text-gray-700 dark:text-white">Total Equity</td>
                                    <td class="py-2 px-2 text-right text-blue-600">{{ Number::currency($totalEquity) }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>

                <div
                    class="mt-12 p-6 bg-gray-50 dark:bg-gray-700/30 rounded-xl border border-gray-200 dark:border-gray-600 text-center">
                    <p class="text-gray-500 dark:text-gray-400 italic">Accounting Equation Check: Assets = Liabilities +
                        Equity</p>
                    <div class="mt-2 text-xl font-bold">
                        {{ Number::currency($totalAssets) }} = {{ Number::currency($totalLiabilities + $totalEquity) }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>