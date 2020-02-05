<?php

namespace Account\Controllers;

use Account\Models\Comment;
use Account\Models\Transaction;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class TransactionCommentModalController extends AccountBaseController
{
    /**
     * @param Request $request
     * @param int $transaction_id
     * @return JsonResponse
     */
    public function index(Request $request, int $transaction_id): JsonResponse
    {
        $transaction = Transaction::with(['comments' => function ($q) {
            $q->with('user')->where('modal_type', Comment::MODAL_TRANSACTION);
        }])->findOrFail($transaction_id);

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

        $transaction->comments()->create([
            'comment' => $comment,
            'user_id' => $request->user()->id,
            'scope' => $request->scope ?? Comment::SCOPE_INTERNAL,
            'modal_type' => Comment::MODAL_TRANSACTION,
        ]);

        return response('OK', 200);
    }
}
