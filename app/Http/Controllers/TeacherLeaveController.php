<?php

namespace App\Http\Controllers;

use Illuminate\View\View;
use App\Models\Teacher;

class TeacherLeaveController extends Controller
{
    public function leaves(): View
    {
        $teachers = Teacher::all();

        return view('leaves.index', compact('teachers'));
    }
}
