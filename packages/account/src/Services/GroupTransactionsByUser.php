<?php

namespace Account\Services;

use Account\Models\Transaction;
use Illuminate\Support\Collection;

class GroupTransactionsByUser
{
    /**
     * @param Collection $transactions
     * @return Collection
     */
    public function __invoke(Collection $transactions): Collection
    {
        $groups = new Collection();

        /** @var Transaction $transaction */
        foreach ($transactions as $transaction) {
            $user_id = data_get($transaction, 'account.user.id', null);
            if (is_null($user_id)) {
                continue;
            }
            if (! $groups->has($user_id)) {
                $groups->put($user_id, (object) [
                    'user' => $transaction->account->user ?? null,
                    'transactions' => new Collection(),
                ]);
            }

            $groups[$user_id]->transactions->push($transaction);
        }

        return $groups;
    }
}
