<?php

namespace App\Filament\Resources\Invoices\Schemas;

use App\Models\Product;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Components\Grid;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Schema;

class InvoiceForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                Grid::make(3)
                    ->schema([
                        TextInput::make('invoice_number')
                            ->default('INV-' . date('YmdHis'))
                            ->required()
                            ->readonly(),
                        DateTimePicker::make('issued_at')
                            ->default(now())
                            ->required(),
                        Toggle::make('scan_mode')
                            ->label('Barcode Scan Mode')
                            ->helperText('Enable to add/increment items via scanner')
                            ->live()
                            ->afterStateUpdated(function ($state) {
                                // This will be handled in the JS/Page layer to set window.barcodeScanModeActive
                            }),
                    ]),

                Repeater::make('items')
                    ->relationship('items')
                    ->schema([
                        Select::make('product_id')
                            ->label('Product')
                            ->options(Product::all()->pluck('name', 'id'))
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Set $set) {
                                $product = Product::find($state);
                                if ($product) {
                                    $set('unit_price', $product->regular_price);
                                    $set('quantity', 1);

                                    $vatRate = (float) ($product->vat_rate ?? 0);
                                    $vatAmount = ($product->regular_price * 1) * ($vatRate / (100 + $vatRate));
                                    $set('vat_amount', round($vatAmount, 2));
                                    $set('subtotal', $product->regular_price);
                                }
                            })
                            ->columnSpan(2),
                        TextInput::make('quantity')
                            ->numeric()
                            ->default(1)
                            ->required()
                            ->reactive()
                            ->afterStateUpdated(function ($state, Get $get, Set $set) {
                                $unitPrice = (float) $get('unit_price');
                                // Fallback to 18 if not specified, but ideally fetch from product
                                $vatRate = 18;
                                $vatAmount = ($unitPrice * $state) * ($vatRate / (100 + $vatRate));
                                $set('vat_amount', round($vatAmount, 2));
                                $set('subtotal', $state * $unitPrice);
                            }),
                        TextInput::make('unit_price')
                            ->numeric()
                            ->prefix('$')
                            ->required()
                            ->readonly(),
                        TextInput::make('vat_amount')
                            ->numeric()
                            ->prefix('$')
                            ->readonly()
                            ->label('VAT Included'),
                        TextInput::make('subtotal')
                            ->numeric()
                            ->prefix('$')
                            ->readonly(),
                    ])
                    ->columns(6)
                    ->columnSpanFull()
                    ->reorderableWithButtons()
                    ->live(), // Important for dynamic updates

                TextInput::make('total_amount')
                    ->label('Grand Total')
                    ->numeric()
                    ->prefix('$')
                    ->readonly()
                    ->placeholder(function (Get $get) {
                        $items = $get('items') ?? [];
                        $total = 0;
                        foreach ($items as $item) {
                            $total += (float) ($item['subtotal'] ?? 0);
                        }
                        return $total;
                    }),
            ]);
    }
}
