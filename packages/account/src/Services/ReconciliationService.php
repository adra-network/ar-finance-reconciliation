<?php

namespace Account\Services;

use Account\Models\Transaction;
use Account\Models\Reconciliation;
use Illuminate\Support\Collection;

class ReconciliationService
{
    /**
     * @param int[] $transaction_ids
     *
     * @throws \Exception
     *
     * @return Reconciliation|null
     */
    public static function reconcileTransactions(array $transaction_ids): ?Reconciliation
    {
        $transactions = Transaction::with('reconciliation')->whereIn('id', $transaction_ids)->get();

        $reconciliation = self::findReconciliationInTransactions($transactions);

        //If there are only 1 or none transactions left, then delete the reconciliation, because its pointless to have one.
        if ($transactions->count() <= 1) {
            if ($reconciliation) {
                $reconciliation->delete();
            }

            return null;
        }

        if (!$reconciliation) {
            $reconciliation = Reconciliation::create([
                'account_id' => $transactions->first()->account_id,
            ]);
        }

        //drop all transactions cause there might be some deleted ones so we attach only the given ones
        $reconciliation->transactions()->update(['reconciliation_id' => null]);
        //reattach the given transactions to reconciliation
        Transaction::whereIn('id', $transaction_ids)->update(['reconciliation_id' => $reconciliation->id]);

        $reconciliation->cacheIsFullyReconciledAttribute();

        return $reconciliation;
    }

    /**
     * @param Collection $transactions
     *
     * @throws \Exception
     *
     * @return Reconciliation|null
     *
     * @internal param $Collection &iterable<Transaction> $transactions
     */
    private static function findReconciliationInTransactions(Collection $transactions): ?Reconciliation
    {
        //check if any of the transactions have a reconciliation
        $reconciliation = null;
        foreach ($transactions as $transaction) {
            //if transaction has a reconciliation and one is already set, that means we have more that one and should abort
            if (!is_null($transaction->reconciliation) && !is_null($reconciliation) && $reconciliation->id !== $transaction->reconciliation->id) {
                throw new \Exception('Can\'t reconcile because given transactions have diferent reconciliations');
            }
            if (!is_null($transaction->reconciliation) && is_null($reconciliation)) {
                $reconciliation = $transaction->reconciliation;
            }
        }

        return $reconciliation;
    }
}
