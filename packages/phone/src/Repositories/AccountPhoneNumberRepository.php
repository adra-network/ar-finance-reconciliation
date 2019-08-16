<?php

namespace Phone\Repositories;

use App\User;
use Phone\Models\AccountPhoneNumber;

class AccountPhoneNumberRepository
{
    public function getNumbersForUser(User $user)
    {
        $query = AccountPhoneNumber::query();
        $query->with('user');
        if (! $user->isAdmin()) {
            $query->where('user_id', $user->id);
        }

        return $query->get();
    }
}
