<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use Illuminate\Http\Request;

class CommentController extends Controller
{


    public function store(Request $request)
    {
        Comment::create([
            'commentable_id' => $request->input('student_id'),
            'commentable_type' => User::class,
            'body' => $request->input('comment'),
            'private' => $request->input('private') ?? 0,
            'author_id' => \backpack_user()->id,

        ]);
    }

    
    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Comment  $comment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Comment $comment)
    {
        $comment->delete();
    }
}
