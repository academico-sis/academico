<?php

namespace App\Http\Controllers;

use Illuminate\Support\Str;
use Illuminate\Http\Request;

class ApiTokenController extends Controller
{
    public function index()
    {
        return view('teacher.api', [
            'user' => backpack_user(),
        ]);
    }

    public function store()
    {
        $user = backpack_user();
        $user->api_token = Str::random(60);
        $user->save();
    }
}
