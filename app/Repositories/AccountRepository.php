<?php

namespace App\Repositories;


use App\Account;
use App\AccountTransaction;
use Illuminate\Support\Collection;

class AccountRepository
{
    /**
     * Rename if anyone has any idea for a normal abstract name
     *
     * @param int $withPreviousMonths
     * @return Collection
     */
    public static function getAccountsForTransactionsIndexPage(int $withPreviousMonths = 0): Collection
    {
        $accounts = Account::with([
            'reconciliations' => function ($q) use ($withPreviousMonths) {
                $q->with('transactions');
                if ($withPreviousMonths > 0) {
                    $q->where('created_at', '>', now()->subMonths($withPreviousMonths)->startOfMonth());
                } else {
                    $q->where('is_fully_reconciled', false);
                }
            },
        ])->get();

        return $accounts;
    }
}
