<?php

namespace Phone\Services;

use App\User;
use Phone\Models\AccountPhoneNumber;

class UserNumberSyncService
{
    /**
     * @var User
     */
    private $user;

    /**
     * UserNumberSyncService constructor.
     * @param User $user
     * @param array $accountNumbers
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param array $accountNumbers
     */
    public function syncAccountNumbers(array $accountNumbers): void
    {
        $this->user->accountPhoneNumbers()->update(['user_id' => null]);
        $this->user->callerPhoneNumbers()->update(['user_id' => null]);
        AccountPhoneNumber::whereIn('id', $accountNumbers)->update(['user_id' => $this->user->id]);
        $this->user->fresh(['accountPhoneNumbers' => function ($q) {
            $q->with(['phoneTransactions' => function ($q) {
                $q->with('callerPhoneNumber');
            }]);
        }, 'callerPhoneNumbers']);

        foreach ($this->user->accountPhoneNumbers as $accountPhoneNumber) {
            foreach ($accountPhoneNumber->phoneTransactions as $transaction) {
                optional($transaction->callerPhoneNumber)->update(['user_id' => $this->user->id]);
            }
        }
    }
}
