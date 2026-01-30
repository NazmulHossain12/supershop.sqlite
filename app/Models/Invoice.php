<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_id',
        'invoice_number',
        'total_amount',
        'total_vat_amount',
        'pdf_path',
        'issued_at',
    ];

    protected $casts = [
        'issued_at' => 'datetime',
    ];

    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function void($reason, $user = null)
    {
        if ($this->status === 'Voided')
            return;

        $user = $user ?? auth()->user();

        $this->update([
            'status' => 'Voided',
            'voided_by' => $user->id,
            'void_reason' => $reason,
        ]);

        // Restock items
        foreach ($this->items as $item) {
            $item->product->increment('stock_quantity', $item->quantity);
        }

        // Ledger Entry for Void
        $saleTransaction = Transaction::where('invoice_id', $this->id)->where('type', 'sale')->first();
        if ($saleTransaction) {
            $voidTransaction = Transaction::create([
                'invoice_id' => $this->id,
                'type' => 'refund',
                'category' => 'voided_sale',
                'amount' => $this->total_amount,
                'description' => "VOIDED: {$this->invoice_number}. Reason: {$reason}",
                'transaction_date' => now(),
            ]);

            // Reverse Ledger Entries (Simplified: Record opposite entries)
            foreach ($saleTransaction->ledgers as $entry) {
                Ledger::create([
                    'transaction_id' => $voidTransaction->id,
                    'account_id' => $entry->account_id,
                    'debit' => $entry->credit,
                    'credit' => $entry->debit,
                    'entry_description' => "Reversal for Voided Invoice {$this->invoice_number}",
                ]);

                // Update Account Balances
                $account = $entry->account;
                if ($entry->debit > 0) {
                    $account->decrement('balance', $entry->debit);
                } else {
                    $account->decrement('balance', $entry->credit);
                }
            }
        }
    }
}
