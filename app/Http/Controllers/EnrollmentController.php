<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use App\Models\Period;
use App\Models\Student;
use App\Models\Course;
use App\Models\Sale;
use App\Models\PreInvoice;


class EnrollmentController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Student $student, Period $period)
    {
        if (!$period->exists) {
            $period = Period::get_default_period();
        }

        // display a list of available courses
        $courses = \App\Models\Course::get_available_courses($period);

        return view('enrollments/create', compact('courses', 'period', 'student'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $course = Course::findOrFail($request->course_id);
        $student = Student::findOrFail($request->student_id);

        $enrollment_id = $student->enroll($course);

        return redirect()->to("/enrollments/$enrollment_id");
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function show(Enrollment $enrollment)
    {
        // if the enrollment has not been invoiced

        if ($enrollment->status_id == 1)
        {
            // if the current enrollment is not part of the cart, add it
            $enrollment->addToCart();
            // add default other products: enrollment fee + books associated to the course, if any (and if they do not already exist in the cart)
            Sale::add_default_products($enrollment->user_id);
        }
        
        $cart = Sale::where('user_id', $enrollment->user_id)->get();
        
        // otherwise, the paid products (if any) will be retrieved from the DB
        //dump($cart);
        // then load the page
        return view('enrollments.show', compact('enrollment', 'cart'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function edit(Enrollment $enrollment)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Enrollment $enrollment)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Enrollment $enrollment)
    {
        //
    }
}
