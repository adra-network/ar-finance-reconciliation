<?php

namespace App\Repositories;

use App\AccountTransaction;
use Illuminate\Support\Collection;

class AccountTransactionRepository
{

    /**
     * @return Collection
     */
    public static function getUnreconciledTransactions($with = []): Collection
    {
        return AccountTransaction::query()
            ->with($with)
            ->leftJoin('reconciliations', 'account_transactions.reconciliation_id', '=', 'reconciliations.id')
            ->where('reconciliations.is_fully_reconciled', false)
            ->orWhere('account_transactions.reconciliation_id', null)
            ->get(['account_transactions.*', 'reconciliations.is_fully_reconciled']);
    }

}