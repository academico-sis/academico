<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Skill;
use App\Models\Result;
use App\Models\Comment;

use App\Models\Enrollment;
use Illuminate\Http\Request;

class ResultController extends Controller
{

    /**
     * Store a newly created result in storage.
     *
     */
    public function store(Request $request)
    {
        $this->middleware(['permission:grades.edit']);

        $result = Result::firstOrNew([
            'enrollment_id' => $request->input('enrollment')
        ]);

        if($request->input('comment') !== null) {
            Comment::create([
                'commentable_id' => $result->id,
                'commentable_type' => Result::class,
                'body' => $request->input('comment'),
                'author_id' => \backpack_user()->id,
            ]);
        }

        $result->result_type_id = $request->input('result');

        $result->save();
    }

    /**
     * Display the specified resource (result for a specific enrollment)
     */
    public function show($enrollment)
    {
        $this->middleware(['permission:grades.view']);

        $enrollment = Enrollment::findOrFail($enrollment);
        
        $grades = $enrollment->grades;
        $skills = $enrollment->skills;
        $comments = $enrollment->result->comments;
        return view('results.show', compact('enrollment', 'grades', 'skills', 'comments'));
    }


}
