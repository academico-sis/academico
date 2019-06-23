<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LeadStatusController extends Controller
{
    public function update(Request $request)
    {
        // create or update the lead status record for the selected student
        $student = Student::findOrFail($request->input('student'));

        $student->lead_type_id = $request->input('status');

        $student->save();

        return $student->lead_type_id;
    }

    public function reset_all_converted_leads()
    {
        DB::table('students')->where('lead_type_id', '=', 1)->update(array('lead_type_id' => 4));
        return back();
    }
}
