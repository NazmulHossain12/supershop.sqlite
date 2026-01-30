<?php

namespace App\Policies;

use App\Models\Invoice;
use App\Models\User;

class InvoicePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Accountant', 'Cashier', 'Auditor']);
    }

    public function view(User $user, Invoice $invoice): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Accountant', 'Auditor']) ||
            ($user->hasRole('Cashier') && $invoice->created_at->isToday());
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Cashier']);
    }

    public function update(User $user, Invoice $invoice): bool
    {
        // Manager can "Void"
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Manager']);
    }

    public function delete(User $user, Invoice $invoice): bool
    {
        // Cashier explicitly CANNOT delete
        if ($user->hasRole('Cashier')) {
            return false;
        }

        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    public function restore(User $user, Invoice $invoice): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }

    public function forceDelete(User $user, Invoice $invoice): bool
    {
        return $user->hasRole('Super Admin');
    }
}
