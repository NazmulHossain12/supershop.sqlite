<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use BackedEnum;

class QuickStockCheck extends Page
{
    protected static \BackedEnum|string|null $navigationIcon = 'heroicon-o-qr-code';

    protected static ?string $navigationLabel = 'Quick Stock Check';

    protected static ?string $title = 'Mobile Stock Scanner';

    protected static \UnitEnum|string|null $navigationGroup = 'Inventory Management';

    protected string $view = 'filament.pages.quick-stock-check';
}
