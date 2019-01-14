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
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $result = Result::firstOrNew([
            'enrollment_id' => $request->input('enrollment')
        ]);

        Comment::create([
            'commentable_id' => $result->id,
            'commentable_type' => Result::class,
            'body' => $request->input('comment'),
            'author_id' => \backpack_user()->id,
        ]);

        $result->result_type_id = $request->input('result');

        $result->save();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Result  $result
     * @return \Illuminate\Http\Response
     */
    public function show($enrollment)
    {
        $enrollment = Enrollment::findOrFail($enrollment);
        
        $grades = $enrollment->grades;
        $skills = $enrollment->skills;
        $comments = $enrollment->result->comments;
        return view('results.show', compact('enrollment', 'grades', 'skills', 'comments'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Result  $result
     * @return \Illuminate\Http\Response
     */
    public function edit(Result $result)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Result  $result
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Result $result)
    {
        //
    }

}
