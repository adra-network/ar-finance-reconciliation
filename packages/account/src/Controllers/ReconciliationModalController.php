<?php

namespace Account\Controllers;

use Account\Models\Account;
use Account\Models\Comment;
use Account\Models\CommentTemplate;
use Account\Models\Reconciliation;
use Account\Models\Transaction;
use Account\Repositories\TransactionRepository;
use Account\Services\ReconciliationService;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

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

        $comments = collect([]);
        $commentTemplates = CommentTemplate::get();

        if ($reconciliation_id) {
            $reconciliation = Reconciliation::with('transactions')->findOrFail($reconciliation_id);

            $transactions = $reconciliation->transactions;
            $transactionsToReconcile = $transactions->pluck('id')->toArray();

            /** @var Account $account */
            $account = Account::with('transactions')->find($reconciliation->account_id);
            $unalocatedTransactions = $account->getUnallocatedTransactionsWithoutGrouping();

            $comments = $reconciliation->comments()->with('user')->when(!$request->user()->isAdmin(), function (Builder $q) {
                $q->isPublic();
            })->where('modal_type', Comment::MODAL_RECONCILIATION)->get();


            return response()->json(['data' => [
                'transactions' => $transactions->merge($unalocatedTransactions),
                'transactionsToReconcile' => $transactionsToReconcile,
                'comments' => $comments,
                'isAdmin' => $request->user()->isAdmin(),
                'commentTemplates' => $commentTemplates,
            ]]);
        }

        if ($transaction_id) {
            $transaction = Transaction::with('reconciliation.transactions')->findOrFail($transaction_id);
            $transactions = Transaction::where('account_id', $transaction->account_id)->get();

            if ($transaction->reconciliation) {
                $reconciliationTransactions = $transaction->reconciliation->transactions->pluck('id');
                $comments = Comment::whereIn('commentable_id', $reconciliationTransactions)->with('user')->when(!$request->user()->isAdmin(), function (Builder $q) {
                    $q->isPublic();
                })->where('modal_type', Comment::MODAL_RECONCILIATION)->get();
            } else {
                $comments = $transaction->comments()->with('user')->when(!$request->user()->isAdmin(), function (Builder $q) {
                    $q->isPublic();
                })->where('modal_type', Comment::MODAL_RECONCILIATION)->get();
            }

            return response()->json(['data' => [
                'transactions' => $transactions,
                'comments' => $comments,
                'isAdmin' => $request->user()->isAdmin(),
                'commentTemplates' => $commentTemplates,
            ]]);
        }

        if ($reference_id && $account_id) {
            $transactions = TransactionRepository::getUnallocatedTransactionsWhereReferenceIdIs($reference_id, $referenceType, $account_id);
            $transactionsToReconcile = $transactions->pluck('id')->toArray();

            /** @var Account $account */
            $account = Account::with('transactions')->find($account_id);
            $unalocatedTransactions = $account->getUnallocatedTransactionsWithoutGrouping();


            return response()->json(['data' => [
                'transactions' => $transactions->merge($unalocatedTransactions),
                'transactionsToReconcile' => $transactionsToReconcile,
                'isAdmin' => $request->user()->isAdmin(),
                'commentTemplates' => $commentTemplates,
            ]]);
        }

        abort(500);
    }

    public function comment(Request $request)
    {
        $reconciliation_id = $request->input("reconciliation_id", null);
        $transaction_id = $request->input('transaction_id', null);

        $data = $request->validate([
            'comment' => ['required'],
        ]);

        $data['user_id'] = $request->user()->id;
        $data['scope'] = $request->user()->isAdmin() ? Comment::SCOPE_INTERNAL : Comment::SCOPE_PUBLIC;
        $data['modal_type'] = Comment::MODAL_RECONCILIATION;

        /** @var Reconciliation|Transaction $entity */
        $entity = $reconciliation_id ? Reconciliation::findOrFail($reconciliation_id) : Transaction::findOrFail($transaction_id);

        $comment = $entity->comments()->create($data);
        $comment->load("user");

        return response()->json(['data' => $comment]);
    }

    public function changeCommentScope(Request $request, int $id)
    {
        abort_if(!$request->user()->isAdmin(), 403, 'Not admin.');

        /** @var Comment $comment */
        $comment = Comment::findOrFail($id);

        $comment->scope = $comment->scope === $comment::SCOPE_PUBLIC ? $comment::SCOPE_INTERNAL : $comment::SCOPE_PUBLIC;
        $comment->save();
        $comment->load('user');

        return response()->json(['data' => $comment]);

    }

    /**
     * @param Request $request
     * @return Response
     * @throws \Exception
     */
    public function reconcile(Request $request): Response
    {
        $request->validate([
            'transactions' => 'required|array',
            'transactions.*' => 'integer',
        ]);
        $transactions = $request->input('transactions');
        $reconciliation = ReconciliationService::reconcileTransactions($transactions);

        $comment = $request->input('comment', null);
        if (!is_null($comment)) {
            $reconciliation->comment = $comment;
            $reconciliation->save();
        }

        return response('OK', 200);
    }
}
