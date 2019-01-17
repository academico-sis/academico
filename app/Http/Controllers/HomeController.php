<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{

    public function __construct()
    {
        $this->middleware(backpack_middleware());
    }

    public function index()
    {
        return view('welcome');
    }

    public function teacher()
    {
        $teacher = backpack_user();
        return view('teacher.dashboard', [
            'teacher' => $teacher,
            'courses' => $teacher->current_courses,
        ]);
    }

}
