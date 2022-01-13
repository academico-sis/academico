<?php

namespace App\Http\Controllers;

use App\Models\PhoneNumber;
use App\Models\Student;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class StudentPhoneNumberController extends Controller
{
    public function get(Student $student)
    {
        return $student->phone;
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Student $student, Request $request)
    {
        return PhoneNumber::create([
            'phoneable_type' => Student::class,
            'phoneable_id' => $student->id,
            'phone_number' => $request->number,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @return Response
     */
    public function destroy(PhoneNumber $phoneNumber)
    {
        $phoneNumber->delete();
    }
}
