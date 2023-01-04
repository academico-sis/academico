<?php

namespace App\Http\Controllers;

use App\Mail\EmailToStudent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

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
        $student = "";
        if($request->get("student")){
            $student = $request->get("student");
        }
        $data = DB::table('students')
            ->select('students.id', 'users.username','users.email')
            ->join('users', 'students.id', '=', 'users.id')
            ->get();
        return view('email.send', [
            'students' => $data,
            'preselect' => $student,
            'course' => $request->get("course")
        ]);
    }


    public function send(Request $request){

        $request->validate([
          //  'student' => 'required|email',
            'subject' => 'required|min:4',
            'message' => 'required',
        ]);
        $email =  $request->get("student");
        $subject = $request->get("subject");
        $message = $request->get("message");
        $courseId = $request->get("courseid");

        if($email == "all"){
            $email = $this->getEmails($courseId);
        }

        Mail::to($email)->send(new EmailToStudent($subject, $message));
        // todo: redirect not working for some reason
        return redirect(route('email-dashboard'))->with('success', 'Profile updated!');
    }

    private function getEmails($courseId){
        $emails = [];
        $data = DB::table('enrollments')
            ->select('users.email')
            ->join('students', 'enrollments.student_id', '=', 'students.id')
            ->join('users', 'users.id', '=', 'students.id')
            ->where('enrollments.course_id','=',$courseId)
            ->get();
        foreach ($data as  $record){
            $emails[] = $record->email;
        }
        return $emails;
    }

}
