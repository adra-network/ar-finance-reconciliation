<?php


namespace Account\Traits;


use Account\Models\Comment;
use Account\Models\Reconciliation;
use Account\Models\Transaction;
use App\User;
use Illuminate\Support\Collection;

trait CommentsHandler
{
    /**
     * @param User $user
     * @return mixed
     */
    public function getCommentsByUserAccess(User $user): Collection
    {
        /** @var Collection $comments */
        $comments = $this->comments;
        if (!$user->isAdmin()) {
            $comments = $comments->reject(function (Comment $comment) {
                return $comment->scope === 'internal';
            });
        }

        if ($this instanceof Reconciliation) {
            /** @var Transaction $transaction */
            foreach ($this->transactions as $transaction) {
                $comments = $comments->merge($transaction->getCommentsByUserAccess($user));
            }
        }

        $comments = $comments->sortByDesc('id');

        return $comments;
    }
}
