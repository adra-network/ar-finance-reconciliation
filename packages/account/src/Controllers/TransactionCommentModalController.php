<?php

namespace Account\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Account\Models\Transaction;
use Illuminate\Http\JsonResponse;

class TransactionCommentModalController extends AccountBaseController
{
    /**
     * @param Request $request
     * @param int $transaction_id
     * @return JsonResponse
     */
    public function index(Request $request, int $transaction_id): JsonResponse
    {
        $transaction = Transaction::findOrFail($transaction_id);

        return response()->json(['data' => $transaction]);
    }

    /**
     * @param Request $request
     * @return Response
     */
    public function update(Request $request): Response
    {
        $transaction_id = $request->input('transaction_id', null);
        $comment = $request->input('comment', null);

        /** @var Transaction $transaction */
        $transaction = Transaction::findOrFail($transaction_id);
        $transaction->updateComment($comment);

        return response('OK', 200);
    }
}
