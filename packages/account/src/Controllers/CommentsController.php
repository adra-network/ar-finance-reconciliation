<?php


namespace Account\Controllers;


use Account\Models\Comment;
use App\Http\Controllers\Controller;

class CommentsController extends Controller
{
    public function destroy(int $comment_id)
    {
        abort_if(!auth()->user()->isAdmin(), 403);
        $comment = Comment::findOrFail($comment_id);
        $comment->delete();
        return response('OK');
    }
}
