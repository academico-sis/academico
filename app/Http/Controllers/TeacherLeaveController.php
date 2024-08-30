<?php

namespace App\Http\Controllers;

use App\Models\Teacher;
use Illuminate\View\View;

class TeacherLeaveController extends Controller
{
    public function leaves(): View
    {
        $teachers = Teacher::all();

        return view('leaves.index', compact('teachers'));
    }
}
