<?php

namespace App\Http\Controllers;

use App\Http\Requests\CommentRequest as StoreRequest;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class CommentController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:comments.edit', ['only' => 'delete']);
    }

    public function store(StoreRequest $request)
    {
        Log::info('Comment created by '.backpack_user()->firstname);

        return Comment::create([
            'commentable_id' => $request->input('commentable_id'),
            'commentable_type' => $request->input('commentable_type'),
            'action' => $request->input('action') ?? 0,
            'body' => $request->input('body'),
            'author_id' => \backpack_user()->id,
        ]);
    }

    public function update(Comment $comment, StoreRequest $request)
    {
        $comment->update([
            'body' => $request->input('body'),
        ]);
    }

    public function destroy(Comment $comment)
    {
        $comment->delete();
    }
}
