<?php

namespace Account\Services;

use App\User;
use Account\Models\Account;

class UserAccountSyncService
{
    /**
     * @param User $user
     * @param $accounts
     */
    public function __invoke(User $user, array $accounts)
    {
        $user->accounts()->update(['user_id' => null]);
        Account::whereIn('id', $accounts)->update(['user_id' => $user->id]);
    }
}
