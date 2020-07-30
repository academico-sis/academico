<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Enrollment;
use App\Models\Scholarship;

class EnrollmentScholarshipController extends Controller
{

    /**
     * Store a newly created resource in storage.
     */
    public function store(Enrollment $enrollment, Request $request)
    {
        $request->validate([
            'scholarship_id' => 'required',
        ]);

        $scholarship = Scholarship::findOrFail($request->scholarship_id);
        $enrollment->addScholarship($scholarship);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Enrollment $enrollment, Request $request)
    {
        $request->validate([
            'scholarship_id' => 'required',
        ]);

        $scholarship = Scholarship::findOrFail($request->scholarship_id);
        $enrollment->removeScholarship($scholarship);
    }
}
