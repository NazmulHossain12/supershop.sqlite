<x-filament-panels::page>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700">
            <h3 class="text-lg font-bold mb-4">Scan Barcode</h3>
            <div id="reader" style="width: 100%;" class="rounded-lg overflow-hidden border"></div>
            <div id="result-message" class="mt-4 text-sm text-gray-500 text-center">Scan a product barcode to see
                details.</div>
        </div>

        <div id="product-card"
            class="bg-white dark:bg-gray-800 p-6 rounded-xl shadow-sm border border-gray-100 dark:border-gray-700 hidden">
            <div class="flex justify-between items-start mb-4">
                <div>
                    <h2 id="product-name" class="text-2xl font-bold text-gray-900 dark:text-white">Product Name</h2>
                    <p id="product-sku" class="text-sm text-gray-500">SKU: 1234567890</p>
                </div>
                <div class="p-3 bg-primary-100 dark:bg-primary-900 rounded-lg">
                    <span id="product-price"
                        class="text-2xl font-bold text-primary-600 dark:text-primary-400">$0.00</span>
                </div>
            </div>

            <div class="space-y-4">
                <div class="flex justify-between pb-2 border-b dark:border-gray-700">
                    <span class="text-gray-500">Brand</span>
                    <span id="product-brand" class="font-bold text-gray-900 dark:text-white">N/A</span>
                </div>
                <div class="flex justify-between pb-2 border-b dark:border-gray-700">
                    <span class="text-gray-500">Category</span>
                    <span id="product-category" class="font-bold text-gray-900 dark:text-white">N/A</span>
                </div>
                <div class="flex justify-between pb-2 border-b dark:border-gray-700">
                    <span class="text-gray-500">Stock Quantity</span>
                    <span id="product-stock" class="font-bold text-gray-900 dark:text-white">0</span>
                </div>
                <div class="flex justify-between pb-2 border-b dark:border-gray-700">
                    <span class="text-gray-500">Supplier</span>
                    <span id="product-supplier" class="font-bold text-gray-900 dark:text-white">N/A</span>
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button onclick="resetScan()"
                    class="flex-1 px-4 py-2 bg-gray-100 dark:bg-gray-700 text-gray-700 dark:text-gray-300 rounded-lg font-bold hover:bg-gray-200 dark:hover:bg-gray-600 transition">
                    Scan Again
                </button>
            </div>
        </div>
    </div>

    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const html5QrCode = new Html5Qrcode("reader");
            const qrCodeSuccessCallback = (decodedText, decodedResult) => {
                fetchProduct(decodedText);
                html5QrCode.pause(); // Pause scanning after success
            };
            const config = { fps: 10, qrbox: { width: 250, height: 250 } };

            html5QrCode.start({ facingMode: "environment" }, config, qrCodeSuccessCallback)
                .catch(err => {
                    console.error("Scanning failed to start", err);
                    document.getElementById('result-message').textContent = 'Camera access denied or not found.';
                });

            window.fetchProduct = function (barcode) {
                document.getElementById('result-message').textContent = 'Searching for ' + barcode + '...';

                fetch('/admin/products/barcode-lookup/' + barcode)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            displayProduct(data.product);
                            document.getElementById('result-message').textContent = 'Last scan: ' + barcode;
                        } else {
                            document.getElementById('result-message').textContent = 'Product not found: ' + barcode;
                            setTimeout(() => html5QrCode.resume(), 2000);
                        }
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        document.getElementById('result-message').textContent = 'Error looking up barcode.';
                        setTimeout(() => html5QrCode.resume(), 2000);
                    });
            }

            function displayProduct(product) {
                document.getElementById('product-name').textContent = product.name;
                document.getElementById('product-sku').textContent = 'SKU: ' + product.sku;
                document.getElementById('product-price').textContent = '$' + product.price;
                document.getElementById('product-brand').textContent = product.brand;
                document.getElementById('product-category').textContent = product.category;
                document.getElementById('product-stock').textContent = product.stock;
                document.getElementById('product-supplier').textContent = product.supplier;
                
                document.getElementById('product-card').classList.remove('hidden');
            }

            window.resetScan = function () {
                document.getElementById('product-card').classList.add('hidden');
                html5QrCode.resume();
            }
        });
    </script>
</x-filament-panels::page>