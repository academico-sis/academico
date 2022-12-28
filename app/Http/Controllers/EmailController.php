<?php

namespace App\Http\Controllers;

use App\Mail\EmailToStudent;
use App\Models\Teacher;
use App\Traits\PeriodSelection;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;

class EmailController extends Controller
{

    public function __construct()
    {
        parent::__construct();
      //  $this->middleware('permission:hr.view', ['except' => 'teacher']);
    }

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $data = DB::table('students')
            ->select('students.id', 'users.username','users.email')
            ->join('users', 'students.id', '=', 'users.id')
            ->get();
        return view('email.send', [
            'students' => $data
        ]);
    }


    public function test(Request $request)
    {
        return redirect(route('email-dashboard'))->with('success', 'Profile updated!');
    }

    public function send(Request $request){
        $email =  $request->get("student");
        $subject = $request->get("subject");
        $message = $request->get("message");
        Mail::to($email)->send(new EmailToStudent($subject, $message));
        // todo: redirect not working for some reason
        return redirect(route('email-dashboard'))->with('success', 'Profile updated!');
    }

}
