<?php

namespace Account\Controllers;

use Account\Models\Account;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Account\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Account\Models\Reconciliation;
use Account\Services\ReconciliationService;
use Account\Repositories\TransactionRepository;

class ReconciliationModalController
{
    /**
     * @param Request $request
     * @return JsonResponse
     */
    public function info(Request $request): JsonResponse
    {
        $transaction_id = $request->input('transaction_id', null);
        $reference_id = $request->input('reference_id', null);
        $referenceType = $request->input('referenceType', 'date');
        $account_id = $request->input('account_id', null);
        $reconciliation_id = $request->input('reconciliation_id', null);
        if (is_null($transaction_id) && (is_null($reference_id) && $referenceType !== 'unallocated') && is_null($reconciliation_id)) {
            abort(404, 'No transaction id found.');
        }

        if ($reconciliation_id) {
            $reconciliation = Reconciliation::with('transactions')->findOrFail($reconciliation_id);

            $transactions = $reconciliation->transactions;
            $transactionsToReconcile = $transactions->pluck('id')->toArray();

            /** @var Account $account */
            $account = Account::with('transactions')->find($reconciliation->account_id);
            $unalocatedTransactions = $account->getUnallocatedTransactionsWithoutGrouping();

            return response()->json(['data' => [
                'transactions'            => $transactions->merge($unalocatedTransactions),
                'transactionsToReconcile' => $transactionsToReconcile,
            ]]);
        }

        if ($transaction_id) {
            $transaction = Transaction::with('reconciliation.transactions')->find($transaction_id);
            $transactions = Transaction::where('account_id', $transaction->account_id)->get();

            return response()->json(['data' => [
                'transactions' => $transactions,
            ]]);
        }

        if ($reference_id && $account_id) {
            $transactions = TransactionRepository::getUnallocatedTransactionsWhereReferenceIdIs($reference_id, $referenceType, $account_id);
            $transactionsToReconcile = $transactions->pluck('id')->toArray();

            /** @var Account $account */
            $account = Account::with('transactions')->find($account_id);
            $unalocatedTransactions = $account->getUnallocatedTransactionsWithoutGrouping();

            return response()->json(['data' => [
                'transactions'            => $transactions->merge($unalocatedTransactions),
                'transactionsToReconcile' => $transactionsToReconcile,
            ]]);
        }

        abort(500);
    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function reconcile(Request $request): Response
    {
        $request->validate([
            'transactions'   => 'required|array',
            'transactions.*' => 'integer',
        ]);
        $transactions = $request->input('transactions');
        $reconciliation = ReconciliationService::reconcileTransactions($transactions);

        $comment = $request->input('comment', null);
        if (! is_null($comment)) {
            $reconciliation->comment = $comment;
            $reconciliation->save();
        }

        return response('OK', 200);
    }
}
