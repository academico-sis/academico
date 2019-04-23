<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\PhoneNumber;
use Illuminate\Http\Request;

class PhoneNumberController extends Controller
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
    public function store(Request $request)
    {
        $number = PhoneNumber::create([
            'phoneable_type' => Student::class,
            'phoneable_id' => $request->student,
            'phone_number' => $request->number,
        ]);
        
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\PhoneNumber  $phoneNumber
     * @return \Illuminate\Http\Response
     */
    public function edit(PhoneNumber $phoneNumber)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\PhoneNumber  $phoneNumber
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, PhoneNumber $phoneNumber)
    {
        //
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
