<?php

namespace App\Http\Controllers\Admin;


use App\AccountTransaction;
use App\Services\ReconciliationService;
use Illuminate\Http\Request;

class TransactionReconciliationController
{
    /**
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function modalInfo(Request $request)
    {
        $id = $request->input('transaction_id', false);
        if (!$id) {
            abort(404, 'No transaction id found.');
        }

        $transaction = AccountTransaction::with('reconciliation.transactions')->find($id);

        $transactions = AccountTransaction::where('account_id', $transaction->account_id)->get();

        return response()->json(['data' => [
            'transactions' => $transactions,
        ]]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Illuminate\Http\Response
     */
    public function reconcileTransactions(Request $request)
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