<?php

namespace App\Http\Controllers;

use App\Models\Teacher;

class TeacherLeaveController extends Controller
{
    public function leaves()
    {
        $teachers = Teacher::all();

        return view('leaves.index', ['teachers' => $teachers]);
    }
}
