<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
                {{ __('Purchase Order') }} #{{ $purchaseOrder->reference_no }}
            </h2>
            <div class="flex items-center gap-4">
                @if($purchaseOrder->status === 'Draft')
                    <a href="{{ route('admin.purchase-orders.edit', $purchaseOrder) }}"
                        class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 active:bg-indigo-700 focus:outline-none transition ease-in-out duration-150">
                        Edit Draft
                    </a>
                @endif
                @if($purchaseOrder->status === 'Draft' || $purchaseOrder->status === 'Cancelled')
                    <form action="{{ route('admin.purchase-orders.destroy', $purchaseOrder) }}" method="POST"
                        onsubmit="return confirm('Are you sure you want to delete this purchase order?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                            class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 active:bg-red-700 focus:outline-none transition ease-in-out duration-150">
                            Delete Order
                        </button>
                    </form>
                @endif
                <a href="{{ route('admin.purchase-orders.index') }}"
                    class="text-sm text-gray-600 dark:text-gray-400 hover:underline">
                    &larr; Back to List
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Left: Order Details -->
                <div class="md:col-span-2 space-y-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold mb-4">Supplier & Order Info</h3>
                            <div class="grid grid-cols-2 gap-4 text-sm">
                                <div>
                                    <p class="text-gray-500 uppercase text-xs font-bold">Supplier</p>
                                    <p class="font-medium">{{ $purchaseOrder->supplier->name }}</p>
                                    <p class="text-gray-600">{{ $purchaseOrder->supplier->email }}</p>
                                    <p class="text-gray-600">{{ $purchaseOrder->supplier->phone }}</p>
                                </div>
                                <div>
                                    <p class="text-gray-500 uppercase text-xs font-bold">Order Details</p>
                                    <p class="font-medium">Ref: {{ $purchaseOrder->reference_no }}</p>
                                    <p class="text-gray-600">Date: {{ $purchaseOrder->created_at->format('M d, Y') }}
                                    </p>
                                    <p class="text-gray-600">Expected:
                                        {{ $purchaseOrder->expected_delivery_date ? \Carbon\Carbon::parse($purchaseOrder->expected_delivery_date)->format('M d, Y') : 'Not set' }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold mb-4">Items</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                                    <thead>
                                        <tr>
                                            <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">
                                                Product</th>
                                            <th
                                                class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                                                Quantity</th>
                                            <th
                                                class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                                                Unit Cost</th>
                                            <th
                                                class="px-4 py-2 text-right text-xs font-medium text-gray-500 uppercase">
                                                Subtotal</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($purchaseOrder->items as $item)
                                            <tr>
                                                <td class="px-4 py-3">{{ $item->product->name }}</td>
                                                <td class="px-4 py-3 text-right">{{ $item->quantity }}</td>
                                                <td class="px-4 py-3 text-right">{{ Number::currency($item->unit_cost) }}
                                                </td>
                                                <td class="px-4 py-3 text-right font-medium">
                                                    {{ Number::currency($item->subtotal) }}
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                    <tfoot>
                                        <tr class="bg-gray-50 dark:bg-gray-900">
                                            <td colspan="3" class="px-4 py-3 text-right font-bold">Total Amount:</td>
                                            <td class="px-4 py-3 text-right font-bold text-lg text-primary-600">
                                                {{ Number::currency($purchaseOrder->total_amount) }}
                                            </td>
                                        </tr>
                                    </tfoot>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right: Actions & Status -->
                <div class="space-y-6">
                    <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-bold mb-4">Actions</h3>
                            <div class="mb-6">
                                <p class="text-sm text-gray-500 mb-1">Current Status</p>
                                @php
                                    $badgeClass = match ($purchaseOrder->status) {
                                        'Draft' => 'bg-gray-100 text-gray-800',
                                        'Ordered' => 'bg-blue-100 text-blue-800',
                                        'Received' => 'bg-green-100 text-green-800',
                                        'Cancelled' => 'bg-red-100 text-red-800',
                                        default => 'bg-gray-100 text-gray-800'
                                    };
                                @endphp
                                <span
                                    class="px-3 py-1 inline-flex text-sm leading-5 font-bold rounded-full {{ $badgeClass }}">
                                    {{ $purchaseOrder->status }}
                                </span>
                            </div>

                            <form action="{{ route('admin.purchase-orders.update-status', $purchaseOrder) }}"
                                method="POST">
                                @csrf
                                @method('PATCH')
                                <div class="mb-4">
                                    <label for="status"
                                        class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">Update
                                        Status</label>
                                    <select name="status" id="status"
                                        class="block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm sm:text-sm">
                                        @foreach(['Draft', 'Ordered', 'Received', 'Cancelled'] as $status)
                                            <option value="{{ $status }}" {{ $purchaseOrder->status === $status ? 'selected' : '' }}>
                                                {{ $status }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit"
                                    class="w-full inline-flex justify-center items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-900 focus:outline-none transition ease-in-out duration-150">
                                    Update Status
                                </button>
                                @if($purchaseOrder->status === 'Received')
                                    <p class="mt-2 text-xs text-green-600 italic">Inventory has been updated.</p>
                                @endif
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>