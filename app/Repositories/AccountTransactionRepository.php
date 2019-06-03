<?php

namespace App\Repositories;

use App\AccountTransaction;
use Illuminate\Support\Collection;

class AccountTransactionRepository
{

    /**
     * todo TEST
     * @param array $with
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

    /**
     * todo TEST
     * Finds all transactions where with provided reference id
     * @param string $reference_id
     * @return Collection
     */
    public static function getUnallocatedTransactionsWhereReferenceIdIs(string $reference_id): Collection
    {
        $transactions = AccountTransaction::whereNull('reconciliation_id')->get();

        return $transactions->filter(function (AccountTransaction $transaction) use ($reference_id) {
            return $transaction->getReferenceId() === $reference_id;
        });
    }
}
