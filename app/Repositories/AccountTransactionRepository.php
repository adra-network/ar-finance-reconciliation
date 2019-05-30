<?php

namespace App\Repositories;

use App\AccountTransaction;
use Illuminate\Support\Collection;

class AccountTransactionRepository
{

    /**
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
     * @return Collection
     */
    public static function getUnallocatedTransactionsWithoutGrouping(): Collection
    {
        $transactions = AccountTransaction::whereNull('reconciliation_id')->get();
        $references = [];

        //Count references, and find the repeating ones.
        // Then filter out the transactions based on that.
        /** @var AccountTransaction $transaction */
        foreach ($transactions as $transaction) {
            $reference_id = $transaction->getReferenceId();

            if (is_null($reference_id)) continue;

            if (!isset($references[$reference_id])) {
                $references[$reference_id] = 0;
            }
            $references[$reference_id]++;
        }

        // remove all transactions that have a reference id and it's count is more than 1,
        // cause that means there is more than one transaction with that reference id
        $transactions = $transactions->reject(function (AccountTransaction $transaction) use ($references) {
            return !is_null($transaction->getReferenceId()) && $references[$transaction->getReferenceId()] > 1;
        });

        return $transactions;
    }


    /**
     * Groups all transactions by reference id
     * @return Collection
     */
    public static function getUnallocatedTransactionGroups(): Collection
    {

        $transactions = AccountTransaction::whereNull('reconciliation_id')->get();
        $groups = [];

        /** @var AccountTransaction $transaction */
        foreach ($transactions as $transaction) {
            $reference_id = $transaction->getReferenceId();
            if (!$reference_id) continue;

            if (!isset($groups[$reference_id])) {
                $groups[$reference_id] = collect([]);
            }

            $groups[$reference_id]->push($transaction);
        }

        $groups = collect($groups)->reject(function($group) {
           return $group->count() < 2;
        });

        return $groups;
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
