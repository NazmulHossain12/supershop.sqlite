<script>
    (function () {
        let barcodeBuffer = '';
        let lastKeyTime = Date.now();

        window.addEventListener('keydown', (e) => {
            // Only process if it looks like a scanner (numeric digits)
            if (e.key >= '0' && e.key <= '9') {
                const currentTime = Date.now();

                // If it's been more than 50ms since last key, it's a new scan
                if (currentTime - lastKeyTime > 50) {
                    barcodeBuffer = '';
                }

                barcodeBuffer += e.key;
                lastKeyTime = currentTime;

                if (barcodeBuffer.length === 13) {
                    const barcode = barcodeBuffer;
                    barcodeBuffer = ''; // Clear immediately

                    console.log('Barcode detected:', barcode);

                    // Priority 1: Invoicing "Scan Mode"
                    // We check if the toggle is present and checked
                    const scanModeToggle = document.querySelector('input[type="checkbox"][id*="scan_mode"]');
                    if (scanModeToggle && scanModeToggle.checked) {
                        console.log('Forwarding to POS Scan Mode');
                        window.dispatchEvent(new CustomEvent('barcode-scanned', { detail: barcode }));
                        return;
                    }

                    // Priority 2: Mobile Stock Check
                    // If the scanner element is present, we are on the Stock Check page
                    if (document.getElementById('reader')) {
                        if (typeof window.fetchProduct === 'function') {
                            window.fetchProduct(barcode);
                            return;
                        }
                    }

                    // Priority 3: Global Redirect
                    console.log('Global Redirecting for barcode:', barcode);
                    window.location.href = `/admin/products/resolve-barcode/${barcode}`;
                }
            } else {
                // Ignore non-numeric keys for buffer, but don't rest immediately if fast
                const currentTime = Date.now();
                if (currentTime - lastKeyTime > 50) {
                    barcodeBuffer = '';
                }
            }
        });
    })();
</script>