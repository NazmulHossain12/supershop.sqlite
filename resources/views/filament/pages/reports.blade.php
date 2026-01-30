<x-filament-panels::page>
    <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
        {{-- Profit & Loss --}}
        <x-filament::section>
            <x-slot name="heading">
                Profit & Loss
            </x-slot>
            <x-slot name="description">
                Income, Expenses, and Net Profit.
            </x-slot>

            <div class="flex gap-4 mt-4">
                <x-filament::button tag="a" href="{{ route('admin.reports.p-and-l.download') }}" target="_blank">
                    View Report
                </x-filament::button>
                <x-filament::button tag="a" color="gray" href="{{ route('admin.reports.p-and-l.download') }}"
                    target="_blank">
                    PDF
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- VAT Report --}}
        <x-filament::section>
            <x-slot name="heading">
                VAT Report
            </x-slot>
            <x-slot name="description">
                Output vs Input Tax.
            </x-slot>

            <div class="flex gap-4 mt-4">
                <x-filament::button tag="a" href="{{ route('admin.reports.vat') }}" target="_blank">
                    View Report
                </x-filament::button>
                <x-filament::button tag="a" color="gray" href="{{ route('admin.reports.vat.download') }}"
                    target="_blank">
                    PDF
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- Inventory Valuation --}}
        <x-filament::section>
            <x-slot name="heading">
                Inventory Valuation
            </x-slot>
            <x-slot name="description">
                Stock value at Cost vs Retail.
            </x-slot>

            <div class="flex gap-4 mt-4">
                <x-filament::button tag="a" href="{{ route('admin.reports.inventory') }}" target="_blank">
                    View Report
                </x-filament::button>
                <x-filament::button tag="a" color="gray" href="{{ route('admin.reports.inventory.download') }}"
                    target="_blank">
                    PDF
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- Balance Sheet --}}
        <x-filament::section>
            <x-slot name="heading">
                Balance Sheet
            </x-slot>
            <x-slot name="description">
                Assets, Liabilities, and Equity.
            </x-slot>

            <div class="flex gap-4 mt-4">
                <x-filament::button tag="a" href="{{ route('admin.reports.balance-sheet') }}" target="_blank">
                    View Report
                </x-filament::button>
                <x-filament::button tag="a" color="gray" href="{{ route('admin.reports.balance-sheet.download') }}"
                    target="_blank">
                    PDF
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- Trial Balance --}}
        <x-filament::section>
            <x-slot name="heading">
                Trial Balance
            </x-slot>
            <x-slot name="description">
                Closing balances of all accounts.
            </x-slot>

            <div class="flex gap-4 mt-4">
                <x-filament::button tag="a" href="{{ route('admin.reports.trial-balance') }}" target="_blank">
                    View Report
                </x-filament::button>
                <x-filament::button tag="a" color="gray" href="{{ route('admin.reports.trial-balance.download') }}"
                    target="_blank">
                    PDF
                </x-filament::button>
            </div>
        </x-filament::section>

        {{-- Cashflow --}}
        <x-filament::section>
            <x-slot name="heading">
                Cashflow
            </x-slot>
            <x-slot name="description">
                Inflows and Outflows.
            </x-slot>

            <div class="flex gap-4 mt-4">
                <x-filament::button tag="a" href="{{ route('admin.reports.cashflow') }}" target="_blank">
                    View Report
                </x-filament::button>
                <x-filament::button tag="a" color="gray" href="{{ route('admin.reports.cashflow.download') }}"
                    target="_blank">
                    PDF
                </x-filament::button>
            </div>
        </x-filament::section>
    </div>
</x-filament-panels::page>