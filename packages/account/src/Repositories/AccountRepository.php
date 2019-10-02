<?php

namespace Account\Repositories;

use App\User;
use Account\Models\Account;
use Account\Models\MonthlySummary;
use Illuminate\Support\Collection;

class AccountRepository
{
    /**
     * @param User $user
     * @return Collection
     */
    public function getAccountsWithUnsyncedSummaries(User $user): Collection
    {
        return $accounts = $user->accounts()->with('monthlySummaries')->get()->filter(function (Account $account) {
            return $account->monthlySummaries->reject(function (MonthlySummary $summary) {
                return $summary->beginning_balance_in_sync;
            })->count() > 0;
        });
    }
}
