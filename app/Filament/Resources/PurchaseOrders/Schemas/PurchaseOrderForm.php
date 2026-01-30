<?php

namespace App\Filament\Resources\PurchaseOrders\Schemas;

use App\Models\Product;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Schemas\Schema;

class PurchaseOrderForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                \Filament\Forms\Components\Section::make('Order Details')
                    ->schema([
                        Select::make('supplier_id')
                            ->relationship('supplier', 'name')
                            ->required()
                            ->searchable()
                            ->preload(),
                        TextInput::make('reference_no')
                            ->required()
                            ->unique(ignoreRecord: true),
                        Select::make('status')
                            ->options([
                                'Draft' => 'Draft',
                                'Ordered' => 'Ordered',
                                'Received' => 'Received',
                                'Cancelled' => 'Cancelled',
                            ])
                            ->default('Draft')
                            ->required(),
                        DatePicker::make('expected_delivery_date'),
                    ])->columns(2),

                \Filament\Forms\Components\Section::make('Items')
                    ->schema([
                        \Filament\Forms\Components\Repeater::make('items')
                            ->relationship()
                            ->schema([
                                Select::make('product_id')
                                    ->label('Product')
                                    ->options(Product::where('status', true)->pluck('name', 'id'))
                                    ->required()
                                    ->searchable()
                                    ->reactive()
                                    ->afterStateUpdated(function ($state, \Filament\Forms\Set $set) {
                                        if ($state) {
                                            $product = Product::find($state);
                                            $set('unit_cost', $product->cost_price ?? 0);
                                        }
                                    }),
                                TextInput::make('quantity')
                                    ->numeric()
                                    ->default(1)
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(
                                        fn($state, \Filament\Forms\Set $set, \Filament\Forms\Get $get) =>
                                        $set('subtotal', $state * $get('unit_cost'))
                                    ),
                                TextInput::make('unit_cost')
                                    ->numeric()
                                    ->required()
                                    ->reactive()
                                    ->afterStateUpdated(
                                        fn($state, \Filament\Forms\Set $set, \Filament\Forms\Get $get) =>
                                        $set('subtotal', $state * $get('quantity'))
                                    ),
                                TextInput::make('subtotal')
                                    ->numeric()
                                    ->disabled()
                                    ->dehydrated()
                                    ->required(),
                            ])
                            ->columns(4)
                            ->defaultItems(1)
                    ]),

                \Filament\Forms\Components\Section::make('Totals')
                    ->schema([
                        TextInput::make('total_amount')
                            ->numeric()
                            ->default(0.0),
                        TextInput::make('total_vat_amount')
                            ->numeric()
                            ->default(0.0),
                        TextInput::make('paid_amount')
                            ->numeric()
                            ->default(0.0),
                    ])->columns(3),
            ]);
    }
}
