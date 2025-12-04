<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserAddress;

class UserAddressPolicy
{
    public function update(User $user, UserAddress $userAddress): bool
    {
        return $user->id === $userAddress->user_id;
    }

    public function delete(User $user, UserAddress $userAddress): bool
    {
        return $user->id === $userAddress->user_id;
    }
}
