<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\User;

use Illuminate\Http\Request;
use App\Http\Requests\CommentRequest as StoreRequest;

class CommentController extends Controller
{

    public function store(StoreRequest $request)
    {
        $this->middleware(['permission:comments.edit']);


        Comment::create([
            'commentable_id' => $request->input('student_id'),
            'commentable_type' => User::class,
            'body' => $request->input('comment'),
            'private' => $request->input('private') ?? 0,
            'author_id' => \backpack_user()->id,
        ]);
    }

    

    public function destroy(Comment $comment)
    {
        $this->middleware(['role:admin']);
        $comment->delete();
    }
}
