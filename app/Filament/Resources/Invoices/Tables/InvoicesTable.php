<?php

namespace App\Filament\Resources\Invoices\Tables;

use App\Models\Invoice;
use App\Models\User;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\DeleteAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class InvoicesTable
{
    public static function configure(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('invoice_number')
                    ->searchable()
                    ->sortable()
                    ->copyable()
                    ->label('Invoice #'),
                TextColumn::make('issued_at')
                    ->dateTime()
                    ->sortable()
                    ->label('Date'),
                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Items'),
                TextColumn::make('total_amount')
                    ->money()
                    ->sortable()
                    ->summarize(\Filament\Tables\Columns\Summarizers\Sum::make()->money())
                    ->label('Grand Total'),
                TextColumn::make('order.order_number')
                    ->label('Order Reference')
                    ->placeholder('POS / Quick Sale')
                    ->toggleable(),
                TextColumn::make('status')
                    ->badge()
                    ->color(fn(string $state): string => match ($state) {
                        'Paid' => 'success',
                        'Voided' => 'danger',
                        default => 'gray',
                    })
                    ->label('Status'),
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                \Filament\Tables\Actions\Action::make('void')
                    ->label('Void')
                    ->icon('heroicon-o-x-circle')
                    ->color('danger')
                    ->requiresConfirmation()
                    ->form([
                        \Filament\Forms\Components\Textarea::make('void_reason')
                            ->required()
                            ->label('Reason for Voiding'),
                    ])
                    ->action(fn(Invoice $record, array $data) => $record->void($data['void_reason']))
                    ->visible(fn(Invoice $record) => $record->status !== 'Voided' && auth()->user()->hasAnyRole(['Super Admin', 'Admin', 'Manager'])),
                \Filament\Tables\Actions\DeleteAction::make()
                    ->visible(fn(User $user) => $user->hasAnyRole(['Super Admin', 'Admin'])),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make()->visible(fn(User $user) => $user->hasAnyRole(['Super Admin', 'Admin'])),
                ]),
            ]);
    }
}
