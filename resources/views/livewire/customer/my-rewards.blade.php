<div class="py-12 bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Header Section -->
        <div class="mb-8 text-center sm:text-left flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-3xl font-black text-gray-900 border-b-4 border-indigo-500 inline-block pb-1">MY REWARDS
                </h1>
                <p class="text-gray-500 mt-2 font-medium">Earn points on every purchase and redeem for discounts!</p>
            </div>
            @if($phone)
                <div class="mt-6 sm:mt-0 flex flex-col items-center sm:items-end">
                    <div class="bg-white p-3 rounded-2xl shadow-xl border border-gray-100 mb-2">
                        {!! \Milon\Barcode\Facades\DNS2DFacade::getBarcodeHTML($phone, 'QRCODE', 4, 4) !!}
                    </div>
                    <span class="text-[10px] font-black text-indigo-600 uppercase tracking-widest">Your Loyalty Scan
                        Code</span>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Stats Sidebar -->
            <div class="md:col-span-1 space-y-6">
                <!-- Points Balance Card -->
                <div
                    class="bg-indigo-600 rounded-[2rem] p-8 text-white shadow-2xl shadow-indigo-200 relative overflow-hidden">
                    <div class="absolute top-0 right-0 -mt-4 -mr-4 w-24 h-24 bg-indigo-500 rounded-full opacity-20">
                    </div>
                    <div class="relative z-10">
                        <p class="text-indigo-100 font-bold uppercase tracking-widest text-xs mb-1">Current Balance</p>
                        <h2 class="text-6xl font-black mb-4">{{ number_format($pointsBalance) }} <span
                                class="text-xl">PTS</span></h2>
                        <div class="border-t border-indigo-500 pt-4 mt-4">
                            <p class="text-indigo-100 text-sm font-medium">Estimated Cash Value</p>
                            <p class="text-2xl font-black text-white">$ {{ number_format($cashValue, 2) }}</p>
                        </div>
                    </div>
                </div>

                <!-- How it Works Card -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <h3 class="font-black text-gray-900 mb-4 uppercase text-sm tracking-widest">How to earn</h3>
                    <div class="space-y-4">
                        <div class="flex items-start space-x-3">
                            <div class="bg-green-100 text-green-600 p-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                            <p class="text-xs text-gray-600 leading-relaxed"><span class="font-bold text-gray-900">Shop
                                    in-store:</span> Show your QR code at checkout to earn points instantly.</p>
                        </div>
                        <div class="flex items-start space-x-3">
                            <div class="bg-blue-100 text-blue-600 p-2 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z">
                                    </path>
                                </svg>
                            </div>
                            <p class="text-xs text-gray-600 leading-relaxed"><span class="font-bold text-gray-900">Shop
                                    online:</span> Points are auto-applied to your account upon purchase completion.</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="md:col-span-2">
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-6 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="font-black text-gray-900 uppercase text-sm tracking-widest">Points History</h3>
                        <div class="bg-gray-50 px-3 py-1 rounded-full text-[10px] font-bold text-gray-500 uppercase">
                            Recent Activity</div>
                    </div>

                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50">
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Date</th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Activity</th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest">
                                        Inv #</th>
                                    <th
                                        class="px-6 py-4 text-[10px] font-black text-gray-400 uppercase tracking-widest text-right">
                                        Points</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                @forelse($transactions as $tx)
                                    <tr class="hover:bg-gray-50/50 transition duration-150">
                                        <td class="px-6 py-4 text-xs font-bold text-gray-500">
                                            {{ $tx->created_at->format('M d, Y') }}</td>
                                        <td class="px-6 py-4">
                                            <p class="text-xs font-black text-gray-900 line-clamp-1">{{ $tx->description }}
                                            </p>
                                        </td>
                                        <td class="px-6 py-4 text-xs font-mono text-indigo-500 font-bold">
                                            {{ $tx->invoice?->invoice_number ?? '-' }}</td>
                                        <td class="px-6 py-4 text-right">
                                            <span
                                                class="inline-block px-3 py-1 rounded-full text-[10px] font-black {{ $tx->type === 'earn' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                                                {{ $tx->type === 'earn' ? '+' : '-' }}{{ number_format($tx->points) }}
                                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                            <svg class="w-12 h-12 mx-auto mb-3 opacity-20" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                            </svg>
                                            <p class="text-sm font-medium">No points activity yet.</p>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    @if($transactions->hasPages())
                        <div class="p-6 border-t border-gray-100 bg-gray-50">
                            {{ $transactions->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>