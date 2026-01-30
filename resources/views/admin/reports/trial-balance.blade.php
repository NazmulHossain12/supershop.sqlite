<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Trial Balance') }}
            </h2>
            <div x-data="{ open: false }" class="relative">
                <button @click="open = !open"
                    class="inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500">
                    Download Report
                    <svg class="ml-2 -mr-0.5 h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <div x-show="open" @click.away="open = false"
                    class="absolute right-0 mt-2 w-48 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-50">
                    <div class="py-1">
                        <a href="{{ route('admin.reports.trial-balance.download', ['format' => 'pdf']) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">PDF Document</a>
                        <a href="{{ route('admin.reports.trial-balance.download', ['format' => 'csv']) }}"
                            class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">CSV Spreadsheet</a>
                    </div>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow-sm sm:rounded-lg border border-gray-100 dark:border-gray-700">
                <div class="p-6">
                    <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                        <thead>
                            <tr class="bg-gray-50 dark:bg-gray-700">
                                <th
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Account</th>
                                <th
                                    class="px-6 py-3 text-left text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Code</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Debit</th>
                                <th
                                    class="px-6 py-3 text-right text-xs font-bold text-gray-500 dark:text-gray-300 uppercase tracking-wider">
                                    Credit</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                            @foreach($accounts as $account)
                                @php
                                    $accountDebits = $account->ledgers()->sum('debit');
                                    $accountCredits = $account->ledgers()->sum('credit');
                                @endphp
                                @if($accountDebits > 0 || $accountCredits > 0)
                                    <tr>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-white">
                                            {{ $account->name }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                            {{ $account->code }}</td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                                            {{ $accountDebits > 0 ? Number::currency($accountDebits) : '-' }}
                                        </td>
                                        <td
                                            class="px-6 py-4 whitespace-nowrap text-sm text-right text-gray-900 dark:text-white">
                                            {{ $accountCredits > 0 ? Number::currency($accountCredits) : '-' }}
                                        </td>
                                    </tr>
                                @endif
                            @endforeach
                        </tbody>
                        <tfoot>
                            <tr class="bg-gray-100 dark:bg-gray-900/50 font-bold border-t-2 border-gray-800">
                                <td colspan="2"
                                    class="px-6 py-4 text-sm text-gray-900 dark:text-white uppercase tracking-widest text-right">
                                    Grand Totals</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-green-600">
                                    {{ Number::currency($totalDebits) }}</td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-right text-red-600">
                                    {{ Number::currency($totalCredits) }}</td>
                            </tr>
                        </tfoot>
                    </table>

                    @if(round($totalDebits, 2) === round($totalCredits, 2))
                        <div class="mt-6 p-4 bg-green-50 text-green-800 rounded-lg text-center font-bold">
                            ✅ Trial Balance is In Balance
                        </div>
                    @else
                        <div class="mt-6 p-4 bg-red-50 text-red-800 rounded-lg text-center font-bold">
                            ❌ Trial Balance is Out of Balance by {{ Number::currency(abs($totalDebits - $totalCredits)) }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>