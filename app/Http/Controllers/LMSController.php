<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ApolearnService;
use Illuminate\Http\Request;

class LMSController extends Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->lms = new ApolearnService();
    }
}
