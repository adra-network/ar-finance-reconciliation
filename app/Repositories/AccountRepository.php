<?php

namespace App\Repositories;


use App\Account;
use Illuminate\Support\Collection;

class AccountRepository
{
    /**
     * Rename if anyone has any idea for a normal abstract name
     *
     * @param int $withPreviousMonths
     * @param int|null $account_id
     * @return Collection
     */
    public static function getAccountsForBatchTableView(int $withPreviousMonths = 0, int $account_id = null): Collection
    {
        $accounts = Account::query()
            ->with([
                'reconciliations' => function ($q) use ($withPreviousMonths) {
                    $q->with('transactions');
                    if ($withPreviousMonths > 0) {
                        $q->where('created_at', '>', now()->subMonths($withPreviousMonths)->startOfMonth());
                    } else {
                        $q->where('is_fully_reconciled', false);
                    }
                },
            ]);

        if ($account_id) {
            $accounts->where('id', $account_id);
        }

        return $accounts->get();
    }
}
