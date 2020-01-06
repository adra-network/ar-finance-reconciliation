<?php


namespace Account\Traits;


use Account\Models\Comment;
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
        $comments = $this->comments->sortByDesc('id');
        if (!$user->isAdmin()) {
            $comments = $comments->reject(function(Comment $comment) {
                return $comment->scope === 'internal';
            });
        }

        return $comments;
    }
}
