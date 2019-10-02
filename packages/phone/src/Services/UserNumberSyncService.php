<?php

namespace Phone\Services;

use App\User;
use Phone\Models\AccountPhoneNumber;

class UserNumberSyncService
{
    /**
     * @param User $user
     * @param array $accountNumbers
     */
    public function __invoke(User $user, array $accountNumbers)
    {
        $user->accountPhoneNumbers()->update(['user_id' => null]);
        $user->callerPhoneNumbers()->update(['user_id' => null]);
        AccountPhoneNumber::whereIn('id', $accountNumbers)->update(['user_id' => $user->id]);

        $user->fresh(['accountPhoneNumbers' => function ($q) {
            $q->with(['phoneTransactions' => function ($q) {
                $q->with('callerPhoneNumber');
            }]);
        }, 'callerPhoneNumbers']);

        foreach ($user->accountPhoneNumbers as $accountPhoneNumber) {
            foreach ($accountPhoneNumber->phoneTransactions as $transaction) {
                optional($transaction->callerPhoneNumber)->update(['user_id' => $user->id]);
            }
        }
    }
}
