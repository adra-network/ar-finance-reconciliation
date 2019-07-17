<?php

namespace Account\Repositories;

use Account\Models\Transaction;
use Illuminate\Support\Collection;

class TransactionRepository
{
    /**
     * @param array $with
     *
     * @return Collection
     */
    public static function getUnreconciledTransactions($with = []): Collection
    {
        return Transaction::query()
            ->with($with)
            ->leftJoin('reconciliations', 'account_transactions.reconciliation_id', '=', 'reconciliations.id')
            ->where('reconciliations.is_fully_reconciled', false)
            ->orWhere('account_transactions.reconciliation_id', null)
            ->get(['account_transactions.*', 'reconciliations.is_fully_reconciled']);
    }

    /**
     * Finds all transactions where with provided reference id.
     *
     * @param string $reference_id
     *
     * @return Collection
     */
    public static function getUnallocatedTransactionsWhereReferenceIdIs(string $reference_id): Collection
    {
        $transactions = Transaction::whereNull('reconciliation_id')->get();

        return $transactions->filter(function (Transaction $transaction) use ($reference_id) {
            return $transaction->getReferenceId() === $reference_id;
        });
    }
}
