<?php

namespace App\Http\Controllers\Admin;

use App\AccountTransaction;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionCommentModalController extends Controller
{
    /**
     * @param Request $request
     * @param int     $transaction_id
     *
     * @return JsonResponse
     */
    public function index(Request $request, int $transaction_id): JsonResponse
    {
        $transaction = AccountTransaction::findOrFail($transaction_id);

        return response()->json(['data' => $transaction]);
    }

    /**
     * @param Request $request
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request): Response
    {
        $transaction_id = $request->input('transaction_id', null);
        $comment = $request->input('comment', null);

        /** @var AccountTransaction $transaction */
        $transaction = AccountTransaction::findOrFail($transaction_id);
        $transaction->updateComment($comment);

        return response('OK', 200);
    }
}
