<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Result;

use App\Models\Comment;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\CommentRequest as StoreRequest;

class CommentController extends Controller
{
     public function __construct()
    {
        $this->middleware('permission:comments.edit', ['only' => 'delete']);
    }

    // todo use CommentRequest instead
    public function store(StoreRequest $request)
    {


        Log::info('Comment created by ' . backpack_user()->firstname);

        Comment::create([
            'commentable_id' => $request->input('commentable_id'),
            'commentable_type' => $request->input('commentable_type'),
            'body' => $request->input('body'),
            'author_id' => \backpack_user()->id,
        ]);
    }

    public function storeresult(Request $request)
    {
        $result = Result::where('enrollment_id', $request->input('enrollment'))->firstOrCreate([
            'enrollment_id' => $request->input('enrollment')
        ]);

        return Comment::create([
            'commentable_id' => $result->id,
            'commentable_type' => Result::class,
            'body' => $request->input('comment'),
            'author_id' => \backpack_user()->id,
        ]);
    }

    

    // todo deduplicate from commentCrudController
    public function destroy(Comment $comment)
    {
        $comment->delete();
    }
}
