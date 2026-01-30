<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function viewAny(User $user): bool
    {
        return true; // Everyone can view products
    }

    public function view(User $user, Product $product): bool
    {
        return true;
    }

    public function create(User $user): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Storekeeper']);
    }

    public function update(User $user, Product $product): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Manager', 'Storekeeper']);
    }

    public function delete(User $user, Product $product): bool
    {
        return $user->hasAnyRole(['Super Admin', 'Admin', 'Manager']);
    }
}
