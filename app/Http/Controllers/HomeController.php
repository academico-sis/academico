<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Exceptions\MissingBaseTables;

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

}
