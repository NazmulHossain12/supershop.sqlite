<?php

namespace App\Livewire\Customer;

use Livewire\Component;
use Livewire\Attributes\Layout;
use Livewire\WithPagination;
use App\Models\Setting;

#[Layout('layouts.app')]
class MyRewards extends Component
{
    use WithPagination;

    public function render()
    {
        $user = auth()->user();
        $pointsBalance = $user->loyalty_points_balance;
        $redemptionValue = (float) Setting::get('redemption_value', 100);
        $cashValue = $pointsBalance / $redemptionValue;

        $transactions = $user->loyaltyTransactions()
            ->with('invoice')
            ->latest()
            ->paginate(10);

        return view('livewire.customer.my-rewards', [
            'pointsBalance' => $pointsBalance,
            'cashValue' => $cashValue,
            'transactions' => $transactions,
            'phone' => $user->phone,
        ]);
    }
}
