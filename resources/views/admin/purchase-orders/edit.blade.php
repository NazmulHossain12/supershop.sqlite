<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Purchase Order') }} #{{ $purchaseOrder->reference_no }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    <form action="{{ route('admin.purchase-orders.update', $purchaseOrder) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                            <div>
                                <label for="supplier_id"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Supplier</label>
                                <select name="supplier_id" id="supplier_id"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required>
                                    @foreach($suppliers as $supplier)
                                        <option value="{{ $supplier->id }}" {{ $purchaseOrder->supplier_id == $supplier->id ? 'selected' : '' }}>{{ $supplier->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="reference_no"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Reference
                                    No</label>
                                <input type="text" name="reference_no" id="reference_no"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    required value="{{ $purchaseOrder->reference_no }}">
                            </div>
                            <div>
                                <label for="expected_delivery_date"
                                    class="block text-sm font-medium text-gray-700 dark:text-gray-300">Expected Delivery
                                    Date</label>
                                <input type="date" name="expected_delivery_date" id="expected_delivery_date"
                                    class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm"
                                    value="{{ $purchaseOrder->expected_delivery_date }}">
                            </div>
                        </div>

                        <div class="mb-6">
                            <h3 class="text-lg font-medium mb-4">Order Items</h3>
                            <div class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700"
                                    id="params-table">
                                    <thead>
                                        <tr>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Product</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Quantity</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Unit Cost</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                VAT Included</th>
                                            <th
                                                class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                                Subtotal</th>
                                            <th class="px-4 py-2"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="items-body" class="divide-y divide-gray-200 dark:divide-gray-700">
                                        @foreach($purchaseOrder->items as $index => $poItem)
                                            <tr class="item-row">
                                                <td class="p-2">
                                                    <select name="items[{{ $index }}][product_id]"
                                                        class="product-select block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm sm:text-sm"
                                                        required>
                                                        @foreach($products as $product)
                                                            <option value="{{ $product->id }}"
                                                                data-cost="{{ $product->cost_price }}"
                                                                data-vat-rate="{{ $product->vat_rate ?? 0 }}"
                                                                {{ $poItem->product_id == $product->id ? 'selected' : '' }}>
                                                                {{ $product->name }}</option>
                                                        @endforeach
                                                    </select>
                                                </td>
                                                <td class="p-2">
                                                    <input type="number" name="items[{{ $index }}][quantity]"
                                                        class="quantity-input block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm sm:text-sm"
                                                        min="1" required value="{{ $poItem->quantity }}">
                                                </td>
                                                <td class="p-2">
                                                    <input type="number" name="items[{{ $index }}][unit_cost]"
                                                        class="cost-input block w-full rounded-md border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 shadow-sm sm:text-sm"
                                                        step="0.01" min="0" required value="{{ $poItem->unit_cost }}">
                                                </td>
                                                <td class="p-2 vat-amount text-sm font-medium text-gray-500">
                                                    {{ number_format($poItem->vat_amount, 2) }}</td>
                                                <td class="p-2 subtotal text-sm font-medium">
                                                    {{ number_format($poItem->subtotal, 2) }}</td>
                                                <td class="p-2">
                                                    <button type="button"
                                                        class="remove-row text-red-600 hover:text-red-900">&times;</button>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            <button type="button" id="add-item"
                                class="mt-4 inline-flex items-center px-4 py-2 bg-gray-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-500 active:bg-gray-700 focus:outline-none transition ease-in-out duration-150">
                                + Add Item
                            </button>
                        </div>

                        <div class="mt-4 flex flex-col items-end gap-2 pr-12">
                            <div class="text-sm">
                                <span class="text-gray-500">Total VAT:</span>
                                <span id="total-vat" class="font-bold ml-2">{{ number_format($purchaseOrder->total_vat_amount, 2) }}</span>
                            </div>
                            <div class="text-lg">
                                <span class="text-gray-700 dark:text-gray-300">Grand Total:</span>
                                <span id="grand-total" class="font-bold ml-2 text-primary-600">{{ number_format($purchaseOrder->total_amount, 2) }}</span>
                            </div>
                        </div>

                        <div class="flex justify-end gap-4 mt-8 pt-4 border-t">
                            <a href="{{ route('admin.purchase-orders.show', $purchaseOrder) }}"
                                class="inline-flex items-center px-4 py-2 bg-gray-200 border border-transparent rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-300 focus:outline-none transition ease-in-out duration-150">
                                Cancel
                            </a>
                            <button type="submit"
                                class="inline-flex items-center px-4 py-2 bg-primary-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary-500 active:bg-primary-700 focus:outline-none transition ease-in-out duration-150">
                                Update Purchase Order
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const itemsBody = document.getElementById('items-body');
            const addItemBtn = document.getElementById('add-item');
            let rowIndex = {{ $purchaseOrder->items->count() }};

            addItemBtn.addEventListener('click', function () {
                const firstRow = document.querySelector('.item-row');
                const row = firstRow.cloneNode(true);
                row.querySelectorAll('input, select').forEach(input => {
                    const name = input.getAttribute('name');
                    input.setAttribute('name', name.replace(/\[\d+\]/, '[' + rowIndex + ']'));
                    input.value = input.tagName === 'SELECT' ? '' : (input.classList.contains('quantity-input') ? 1 : '');
                });
                row.querySelector('.subtotal').textContent = '0.00';
                itemsBody.appendChild(row);
                rowIndex++;
            });

            itemsBody.addEventListener('change', function (e) {
                if (e.target.classList.contains('product-select')) {
                    const option = e.target.options[e.target.selectedIndex];
                    const cost = option.dataset.cost;
                    const row = e.target.closest('.item-row');
                    const costInput = row.querySelector('.cost-input');
                    if (cost) costInput.value = cost;
                    calculateSubtotal(row);
                }
                if (e.target.classList.contains('quantity-input') || e.target.classList.contains('cost-input')) {
                    calculateSubtotal(e.target.closest('.item-row'));
                }
            });

            itemsBody.addEventListener('click', function (e) {
                if (e.target.classList.contains('remove-row')) {
                    const rows = document.querySelectorAll('.item-row');
                    if (rows.length > 1) {
                        e.target.closest('.item-row').remove();
                    }
                }
            });

            function calculateSubtotal(row) {
                const qty = parseFloat(row.querySelector('.quantity-input').value) || 0;
                const cost = parseFloat(row.querySelector('.cost-input').value) || 0;
                const select = row.querySelector('.product-select');
                const option = select.options[select.selectedIndex];
                const vatRate = option ? parseFloat(option.dataset.vatRate || 0) : 0;

                const subtotal = (qty * cost);
                const vatAmount = subtotal * (vatRate / (100 + vatRate));

                row.querySelector('.subtotal').textContent = subtotal.toFixed(2);
                row.querySelector('.vat-amount').textContent = vatAmount.toFixed(2);

                calculateGrandTotal();
            }

            function calculateGrandTotal() {
                let total = 0;
                let totalVat = 0;
                document.querySelectorAll('.item-row').forEach(row => {
                    total += parseFloat(row.querySelector('.subtotal').textContent) || 0;
                    totalVat += parseFloat(row.querySelector('.vat-amount').textContent) || 0;
                });
                document.getElementById('total-vat').textContent = totalVat.toFixed(2);
                document.getElementById('grand-total').textContent = total.toFixed(2);
            }

            // Initialize on load
            calculateGrandTotal();
        });
    </script>
</x-app-layout>