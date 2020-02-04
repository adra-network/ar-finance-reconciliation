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
     * @param string $modal_type
     *
     * @return mixed
     */
    public function getCommentsByUserAccess(User $user, $modal_type = NULL): Collection
    {
        /** @var Collection $comments */
        $comments = $this->comments;
        if (!is_null($modal_type)) {
            $comments = $comments->reject(function (Comment $comment) use ($modal_type) {
                return $comment->modal_type != $modal_type;
            });
        }

        if (!$user->isAdmin()) {
            $comments = $comments->reject(function (Comment $comment) {
                return $comment->scope === 'internal';
            });
        }

        if ($this instanceof Reconciliation) {
            /** @var Transaction $transaction */
            foreach ($this->transactions as $transaction) {
                $comments = $comments->merge($transaction->getCommentsByUserAccess($user, 'reconciliation'));
            }
        }

        $comments = $comments->sortByDesc('id');

        return $comments;
    }
}
