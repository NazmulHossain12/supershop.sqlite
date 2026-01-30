<?php

namespace App\Policies;

use App\Models\Supplier;
use App\Models\User;

class SupplierPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Storekeeper', 'Auditor']);
    }

    public function view(User $user, Supplier $supplier): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Storekeeper', 'Auditor']);
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Storekeeper']);
    }

    public function update(User $user, Supplier $supplier): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Storekeeper']);
    }

    public function delete(User $user, Supplier $supplier): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin']);
    }
}
