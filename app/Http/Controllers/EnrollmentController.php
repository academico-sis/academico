<?php

namespace App\Http\Controllers;

use App\Models\Enrollment;
use Illuminate\Http\Request;
use App\Models\Period;
use App\Models\User;
use App\Models\Course;
use App\Models\Cart;
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
    public function create(User $student, Period $period)
    {
        if (!$period->exists) {
            $period = Period::get_default_period();
        }

        // display a list of available courses
        $courses = Course::get_available_courses($period);

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
        $student = User::student()->findOrFail($request->student_id);

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
        
        // load the products from the invoice tables
        $products = $enrollment->pre_invoice()
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
    public function bill(Enrollment $enrollment)
    {
        // if the current enrollment is not part of the cart, add it
        $enrollment->addToCart();

        // add default other products: enrollment fee + books associated to the course, if any (and if they do not already exist in the cart)
        Cart::add_default_products($enrollment->user_id);

        // the pending products (if any) will also be retrieved from the DB
        $products = Cart::where('user_id', $enrollment->user_id)->get();

        // show the cart for the user
        return redirect("carts/$enrollment->user_id");
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
     * Change the status of the enrollment to ANULADO
     * 
     * Later, use softdeletes instead?
     *
     * @param  \App\Models\Enrollment  $enrollment
     * @return \Illuminate\Http\Response
     */
    public function destroy(Enrollment $enrollment)
    {
        if ($enrollment->status == 1) {
            $enrollment->status = 3;
            $enrollment->save();
        }
    }
}
