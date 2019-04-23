<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DataUpdateController extends Controller
{
    public function index()
    {
        $user = backpack_user();

        return view('student/update', [
            'user' => $user,
        ]);
    }

    public function index2()
    {
        $user = backpack_user();

        return view('student/update2', [
            'user' => $user,
        ]);
    }

    public function index3()
    {
        $user = backpack_user();

        return view('student/update3', [
            'user' => $user,
        ]);
    }

    public function index4()
    {
        $user = backpack_user();

        return view('student/update4', [
            'user' => $user,
        ]);
    }

    public function index5()
    {
        $user = backpack_user();

        return view('student/update5', [
            'user' => $user,
        ]);
    }

    public function index6()
    {
        $user = backpack_user();

        return view('student/update6', [
            'user' => $user,
        ]);
    }
}
