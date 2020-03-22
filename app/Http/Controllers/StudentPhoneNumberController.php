<?php

namespace App\Http\Controllers;

use App\Models\PhoneNumber;
use App\Models\Student;
use Illuminate\Http\Request;

class StudentPhoneNumberController extends Controller
{
    public function get(Student $student)
    {
        return $student->phone;
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Student $student, Request $request)
    {
        $number = PhoneNumber::create([
            'phoneable_type' => Student::class,
            'phoneable_id' => $student->id,
            'phone_number' => $request->number,
        ]);
    }


    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\PhoneNumber  $phoneNumber
     * @return \Illuminate\Http\Response
     */
    public function destroy(PhoneNumber $phoneNumber)
    {
        $phoneNumber->delete();
    }
}
