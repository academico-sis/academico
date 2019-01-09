<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Comment;

class AdministrativeCommentController extends Controller
{
    public function store(Request $request)
    {
        Comment::create([
            'commentable_id' => $request->input('student_id'),
            'commentable_type' => User::class,
            'body' => $request->input('comment'),
            'private' => true,
            'author_id' => \backpack_user()->id,

        ]);
    }
}
