<?php

namespace App\Filament\Resources\Products\Schemas;

use App\Models\Product;
use Filament\Actions\Action;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Set;
use Filament\Schemas\Schema;

class ProductForm
{
    public static function configure(Schema $schema): Schema
    {
        return $schema
            ->components([
                TextInput::make('name')
                    ->required(),
                TextInput::make('slug')
                    ->required(),
                TextInput::make('sku')
                    ->label('SKU')
                    ->required(),
                TextInput::make('barcode')
                    ->unique(Product::class, 'barcode', ignoreRecord: true),
                // ->suffixAction(
                //     Action::make('generate')
                //         ->icon('heroicon-m-arrow-path')
                //         ->tooltip('Generate Random Barcode')
                //         ->action(function (Set $set) {
                //             $set('barcode', '200' . str_pad((string) mt_rand(0, 9999999999), 10, '0', STR_PAD_LEFT));
                //         })
                // ),
                \Filament\Forms\Components\Select::make('supplier_id')
                    ->relationship('supplier', 'name')
                    ->searchable()
                    ->placeholder('Select a supplier'),
                Textarea::make('description')
                    ->columnSpanFull(),
                Textarea::make('short_description')
                    ->columnSpanFull(),
                TextInput::make('category_id')
                    ->required()
                    ->numeric(),
                TextInput::make('brand_id')
                    ->numeric(),
                TextInput::make('cost_price')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('regular_price')
                    ->required()
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('sale_price')
                    ->numeric()
                    ->prefix('$'),
                TextInput::make('stock_quantity')
                    ->required()
                    ->numeric()
                    ->default(0),
                TextInput::make('unit')
                    ->required()
                    ->default('pcs'),
                Toggle::make('tax_applicable')
                    ->required(),
                TextInput::make('vat_rate')
                    ->label('VAT Rate')
                    ->numeric()
                    ->default(0)
                    ->suffix('%')
                    ->required(),
                Toggle::make('featured')
                    ->required(),
                Toggle::make('status')
                    ->required(),
                FileUpload::make('image_url')
                    ->image(),
            ]);
    }
}
