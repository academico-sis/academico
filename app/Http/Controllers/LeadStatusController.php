<?php

namespace App\Http\Controllers;

use App\Models\Student;
use Illuminate\Http\Request;

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
}
