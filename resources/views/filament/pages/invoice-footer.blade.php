<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Sync scan mode from Livewire to window
        const syncScanMode = () => {
            const scanModeToggle = document.querySelector('input[type="checkbox"][id*="scan_mode"]');
            if (scanModeToggle) {
                window.barcodeScanModeActive = scanModeToggle.checked;
            }
        };

        // Listen for barcode-scanned from the global listener
        window.addEventListener('barcode-scanned', (event) => {
            console.log('Forwarding scan to Livewire:', event.detail);
            // find the livewire component
            const livewire = window.Livewire.find(
                document.querySelector('[wire\\:id]').getAttribute('wire:id')
            );
            if (livewire) {
                livewire.dispatch('barcode-scanned', { barcode: event.detail });
            }
        });

        // Set initial state
        setTimeout(syncScanMode, 1000);

        // Update when toggle changes
        document.addEventListener('change', (e) => {
            if (e.target.id && e.target.id.includes('scan_mode')) {
                window.barcodeScanModeActive = e.target.checked;
                console.log('Scan Mode set to:', window.barcodeScanModeActive);
            }
        });
    });
</script>