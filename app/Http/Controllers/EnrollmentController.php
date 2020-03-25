<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnrollmentRequest;
use App\Models\Attendance;
use App\Models\Book;
use App\Models\Course;
use App\Models\Discount;
use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\Paymentmethod;
use App\Models\Student;
use App\Traits\PeriodSelection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;

class EnrollmentController extends Controller
{
    use PeriodSelection;

    public function __construct()
    {
        parent::__construct();

        // these methods are reserved to administrators or staff members.
        // Only the store method can also be called by teachers to enroll students in their courses
        $this->middleware('permission:enrollments.edit', ['except' => 'store']);
    }

    /**
     * Store the newly created enrollment.
     */
    public function store(StoreEnrollmentRequest $request)
    {
        $course = Course::findOrFail($request->input('course_id'));

        if (Gate::forUser(backpack_user())->denies('enroll-in-course', $course)) {
            abort(403);
        }

        $student = Student::findOrFail($request->input('student_id'));
        $enrollment_id = $student->enroll($course);
        \Alert::success(__('Enrollment successfully created'))->flash();

        Log::info(backpack_user()->firstname.' generated a new enrollment for student '.$student->name);

        if (backpack_user()->can('enrollments.edit')) {
            return redirect()->to("/enrollment/$enrollment_id/show");
        }
    }

    public function update(Enrollment $enrollment, Request $request)
    {
        $course = Course::findOrFail($request->input('course_id'));

        // if enrollment has children, delete them
        Enrollment::where('parent_id', $enrollment->id)->delete();

        // update enrollment with new course
        $enrollment->update([
            'course_id' => $course->id,
        ]);

        // if the new course has children, create an enrollment as well
        foreach ($course->children as $children_course) {
            $child_enrollment = Enrollment::firstOrNew([
                'student_id' =>  $enrollment->student_id,
                'course_id' => $children_course->id,
                'parent_id' => $enrollment->id,
            ]);
            $child_enrollment->responsible_id = backpack_user()->id ?? null;
            $child_enrollment->save();
        }

        // delete attendance
        foreach ($enrollment->course->events as $event) {
            Attendance::where('event_id', $event->id)->where('student_id', $enrollment->student_id)->delete();
        }

        foreach ($enrollment->course->children as $child) {
            foreach ($child->events as $event) {
                Attendance::where('event_id', $event->id)->where('student_id', $enrollment->student_id)->delete();
            }
        }

        // TODO delete grades and/or skills

        // display a confirmation message and redirect to enrollment details
        \Alert::success(__('The enrollment has been updated'))->flash();

        return redirect("enrollment/$enrollment->id/show");
    }

    /**
     * Create a new cart with the specified enrollment
     * and display the cart.
     */
    public function bill(Enrollment $enrollment)
    {
        Log::info(backpack_user()->firstname.' is generating a preinvoice');

        $enrollments = Enrollment::where('id', $enrollment->id)->with('course.rhythm')->get();
        $books = $enrollment->course->books ?? [];
        $fees = Fee::first()->get(); // todo issue #119

        $availableBooks = Book::all();
        $availableFees = Fee::all();
        $availableDiscounts = Discount::all();
        $contactData = $enrollment->student->contacts;
        $availablePaymentMethods = Paymentmethod::all();

        return view('carts.show', compact('enrollments', 'fees', 'books', 'availableBooks', 'availableFees', 'availableDiscounts', 'contactData', 'availablePaymentMethods'));
    }
}
