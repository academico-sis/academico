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
     * Display a list of enrollments that need attention
     * Pending enrollments 
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $enrollments = Enrollment::pending();
        return view('enrollments/index', compact('enrollments'));
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
        
        // otherwise load the products from the invoice tables
        $products = PreInvoice::where('enrollment_id', $enrollment->id)
        ->with('pre_invoice_details')
        ->get();
        
        // then load the page
        return view('enrollments.show', compact('enrollment', 'products'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function invoice(Enrollment $enrollment)
    {
        // if the current enrollment is not part of the cart, add it
        $enrollment->addToCart();

        // add default other products: enrollment fee + books associated to the course, if any (and if they do not already exist in the cart)
        Sale::add_default_products($enrollment->user_id);

        // the pending products (if any) will also be retrieved from the DB
        $products = Sale::where('user_id', $enrollment->user_id)->get();

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
