<?php

namespace App\Http\Controllers\Admin;


use App\AccountTransaction;
use App\Repositories\AccountTransactionRepository;
use App\Services\ReconciliationService;
use Illuminate\Http\Request;

class ReconciliationModalController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function info(Request $request)
    {
        $transaction_id = $request->input('transaction_id', false);
        $reference_id = $request->input('reference_id', false);
        if (!$transaction_id && !$reference_id) {
            abort(404, 'No transaction id found.');
        }

        if ($transaction_id) {
            $transaction = AccountTransaction::with('reconciliation.transactions')->find($transaction_id);
            $transactions = AccountTransaction::where('account_id', $transaction->account_id)->get();

            return response()->json(['data' => [
                'transactions' => $transactions,
            ]]);
        }
        if ($reference_id) {
            $transactions = AccountTransactionRepository::getUnallocatedTransactionsWhereReferenceIdIs($reference_id);
            $transactionsToReconcile = $transactions->pluck('id')->toArray();
            $unalocatedTransactions = AccountTransactionRepository::getUnallocatedTransactionsWithoutGrouping();

            return response()->json(['data' => [
                'transactions' => $transactions->merge($unalocatedTransactions),
                'transactionsToReconcile' => $transactionsToReconcile,
            ]]);
        }

        abort(500);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function reconcile(Request $request)
    {
        $request->validate([
            'transactions' => 'required|array',
            'transactions.*' => 'integer',
        ]);
        $transactions = $request->input('transactions');
        $reconciliation = ReconciliationService::reconcileTransactions($transactions);

        $comment = $request->input('comment', false);
        if ($comment) {
            $reconciliation->comment = $comment;
            $reconciliation->save();
        }

        return response('OK', 200);
    }

}