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
     * Show the page for creating a new enrollment for the specified student
     *
     */
    public function create(User $student, Request $request)
    {
        $this->middleware(['permission:enrollments.create']);

        if($request->query('period') !== null) {
            $period = Period::findOrFail($request->query('period'));
        }
        else {
            $period = Period::get_default_period();
        }

        // display a list of available courses
        $courses = Course::get_available_courses($period);

        return view('enrollments/create', compact('courses', 'period', 'student'));
    }

    /**
     * Store the newly created enrollment
     */
    public function store(Request $request)
    {
        $this->middleware(['permission:enrollments.create']);

        $course = Course::findOrFail($request->input('course_id'));
        $student = User::findOrFail($request->input('student_id'));
        $enrollment_id = $student->enroll($course);
        return redirect()->to("/enrollment/$enrollment_id");
    }


    /**
     * Show the page for billing the specified enrollment
     */
    public function bill(Enrollment $enrollment)
    {
        $this->middleware(['permission:enrollments.edit']);

        // if the current enrollment is not part of the cart, add it
        $enrollment->addToCart();

        // add default other products: enrollment fee + books associated to the course, if any (and if they do not already exist in the cart)
        Cart::add_default_products($enrollment->user_id);

        // the pending products (if any) will also be retrieved from the DB
        $products = Cart::where('user_id', $enrollment->user_id)->get();

        // show the cart for the user
        return redirect("cart/$enrollment->user_id");
    }


}
