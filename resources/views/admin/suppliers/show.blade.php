<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Supplier Details') }}: {{ $supplier->name }}
            </h2>
            <a href="{{ route('admin.suppliers.index') }}"
                class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                &larr; Back to List
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Supplier Info Card -->
                <div class="md:col-span-1 space-y-6">
                    <div
                        class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg border-l-4 border-primary-500">
                        <div class="p-6">
                            <div class="flex flex-col items-center mb-6">
                                <div
                                    class="w-20 h-20 bg-primary-100 rounded-full flex items-center justify-center text-primary-600 text-3xl font-bold mb-3">
                                    {{ substr($supplier->name, 0, 1) }}
                                </div>
                                <h3 class="text-xl font-bold text-center">{{ $supplier->name }}</h3>
                                <p class="text-sm text-gray-500">{{ $supplier->contact_person }}</p>
                            </div>

                            <div class="space-y-4 text-sm">
                                <div class="flex justify-between border-b pb-2">
                                    <span class="text-gray-500 font-bold uppercase text-xs">Current Balance</span>
                                    <span
                                        class="font-bold {{ $supplier->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                                        {{ Number::currency($supplier->current_balance) }}
                                    </span>
                                </div>
                                <div>
                                    <span class="text-gray-500 font-bold uppercase text-xs">Email</span>
                                    <p>{{ $supplier->email ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500 font-bold uppercase text-xs">Phone</span>
                                    <p>{{ $supplier->phone ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500 font-bold uppercase text-xs">Tax Number</span>
                                    <p>{{ $supplier->tax_number ?? '-' }}</p>
                                </div>
                                <div>
                                    <span class="text-gray-500 font-bold uppercase text-xs">Address</span>
                                    <p>{{ $supplier->address ?? '-' }}</p>
                                </div>
                            </div>

                            <div class="mt-8">
                                <a href="{{ route('admin.suppliers.edit', $supplier) }}"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none transition ease-in-out duration-150">
                                    Edit Supplier
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Purchase Order History -->
                <div class="md:col-span-2">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold mb-4">Purchase Order History</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                Ref No</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                Status</th>
                                            <th
                                                class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                                                Total</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                Date</th>
                                            <th class="px-4 py-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($purchaseOrders as $order)
                                            <tr>
                                                <td class="px-4 py-3">{{ $order->reference_no }}</td>
                                                <td class="px-4 py-3">
                                                    @php
                                                        $badgeClass = match ($order->status) {
                                                            'Draft' => 'bg-gray-100 text-gray-800',
                                                            'Ordered' => 'bg-blue-100 text-blue-800',
                                                            'Received' => 'bg-green-100 text-green-800',
                                                            'Cancelled' => 'bg-red-100 text-red-800',
                                                            default => 'bg-gray-100 text-gray-800'
                                                        };
                                                    @endphp
                                                    <span
                                                        class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $badgeClass }}">
                                                        {{ $order->status }}
                                                    </span>
                                                </td>
                                                <td class="px-4 py-3 text-right font-medium">
                                                    {{ Number::currency($order->total_amount) }}</td>
                                                <td class="px-4 py-3 text-sm">{{ $order->created_at->format('M d, Y') }}
                                                </td>
                                                <td class="px-4 py-3 text-right">
                                                    <a href="{{ route('admin.purchase-orders.show', $order) }}"
                                                        class="text-primary-600 hover:text-primary-900">View</a>
                                                </td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="5" class="px-4 py-4 text-center text-gray-500 italic">No
                                                    purchase orders from this supplier yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $purchaseOrders->links() }}
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg mt-6">
                        <div class="p-6">
                            <h3 class="text-lg font-bold mb-4">Payment History</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                Date</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                PO Ref</th>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                Method</th>
                                            <th
                                                class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                                                Amount</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @forelse($payments as $payment)
                                            <tr>
                                                <td class="px-4 py-3 text-sm">
                                                    {{ \Carbon\Carbon::parse($payment->payment_date)->format('M d, Y') }}
                                                </td>
                                                <td class="px-4 py-3 text-sm">
                                                    @if($payment->purchaseOrder)
                                                        <a href="{{ route('admin.purchase-orders.show', $payment->purchaseOrder) }}"
                                                            class="text-indigo-600 hover:underline">
                                                            {{ $payment->purchaseOrder->reference_no }}
                                                        </a>
                                                    @else
                                                        -
                                                    @endif
                                                </td>
                                                <td class="px-4 py-3 text-sm">{{ $payment->payment_method }}</td>
                                                <td class="px-4 py-3 text-right text-sm font-bold">
                                                    {{ Number::currency($payment->amount) }}</td>
                                            </tr>
                                        @empty
                                            <tr>
                                                <td colspan="4" class="px-4 py-4 text-center text-gray-500 italic">No
                                                    payments recorded yet.</td>
                                            </tr>
                                        @endforelse
                                    </tbody>
                                </table>
                            </div>
                            <div class="mt-4">
                                {{ $payments->links() }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>