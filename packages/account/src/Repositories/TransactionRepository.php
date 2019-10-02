<?php

namespace Account\Repositories;

use Account\Models\Transaction;
use Illuminate\Support\Collection;
use Illuminate\Database\Eloquent\Builder;
use Account\TransactionAlertSystem\Intervals;
use Account\DTO\TransactionReconciliationGroupData;

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
     * @param string $referenceType
     * @param int|null $account_id
     * @return Collection
     */
    public static function getUnallocatedTransactionsWhereReferenceIdIs(string $reference_id, string $referenceType = 'date', int $account_id = null): Collection
    {
        /** @var Collection $transactions */
        $transactions = Transaction::whereNull('reconciliation_id')->when($account_id, function ($q) use ($account_id) {
            $q->where('account_id', $account_id);
        })->get();

        $transactions = $transactions->filter(function (Transaction $transaction) use ($reference_id, $referenceType) {
            if ($referenceType === TransactionReconciliationGroupData::TYPE_DATE) {
                $ref = $transaction->getReferenceId()->getDateString();
            }
            if ($referenceType === TransactionReconciliationGroupData::TYPE_TA) {
                $ref = $transaction->getReferenceId()->getTa();
            }

            return isset($ref) && $ref === $reference_id ? true : false;
        });

        return $transactions;
    }

    /**
     * @param int|null $user_id
     * @return Collection
     */
    public static function getLateTransactions(int $user_id = null): Collection
    {
        $intervals = new Intervals();
        $query = Transaction::query()->with('account', 'reconciliation');

        foreach ($intervals->getIntervals() as $interval) {
            $query->orWhere(function (Builder $q) use ($interval) {
                if ($interval->min) {
                    $q->whereDate('transaction_date', '<=', $interval->getMinInCarbon());
                }
                if ($interval->max) {
                    $q->whereDate('transaction_date', '>', $interval->getMaxInCarbon());
                }
            });
        }

        $transactions = $query->get();
        $transactions = $transactions->reject(function ($transaction) {
            return data_get($transaction, 'reconciliation.is_fully_reconciled', false);
        });

        if ($user_id) {
            $transactions = $transactions->reject(function ($transaction) use ($user_id) {
                return data_get($transaction, 'account.user.id') !== $user_id;
            });
        }

        return $transactions;
    }
}
