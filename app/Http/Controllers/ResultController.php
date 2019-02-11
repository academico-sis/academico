<?php

namespace App\Http\Controllers;

use App\Models\Grade;
use App\Models\Skills\Skill;
use App\Models\Result;
use App\Models\Comment;

use App\Models\Enrollment;
use Illuminate\Http\Request;

class ResultController extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:evaluation.edit', ['only' => ['store']]);
    }

    /**
     * Store a newly created result in storage.
     *
     */
    public function store(Request $request)
    {

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
        $enrollment = Enrollment::findOrFail($enrollment);
        
        if(backpack_user()->can('evaluation.view') || backpack_user()->id == $enrollment->student->id)
        {
            $grades = $enrollment->grades;
            $skills = $enrollment->skills;
            $result = $enrollment->result;
            return view('results.show', compact('enrollment', 'grades', 'skills', 'result'));
        }
        else {
            abort(403);
        }
    }


}
