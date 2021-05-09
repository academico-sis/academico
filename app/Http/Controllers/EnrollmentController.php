<?php

namespace App\Http\Controllers;

use App\Events\EnrollmentUpdated;
use App\Http\Requests\StoreEnrollmentRequest;
use App\Models\Attendance;
use App\Models\Book;
use App\Models\Course;
use App\Models\Discount;
use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\InvoiceType;
use App\Models\Paymentmethod;
use App\Models\Student;
use App\Traits\PeriodSelection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Prologue\Alerts\Facades\Alert;

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
        Alert::success(__('Enrollment successfully created'))->flash();

        Log::info(backpack_user()->firstname.' generated a new enrollment for student '.$student->name);

        if (backpack_user()->can('enrollments.edit')) {
            return url("/enrollment/$enrollment_id/show");
        }
    }

    public function update(Enrollment $enrollment, Request $request)
    {
        $course = Course::findOrFail($request->input('course_id'));
        $previousCourse = $enrollment->course;

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

        // update LMS
        EnrollmentUpdated::dispatch($enrollment->student, $previousCourse, $course);

        // display a confirmation message and redirect to enrollment details
        Alert::success(__('The enrollment has been updated'))->flash();

        return "enrollment/$enrollment->id/show";
    }

    /**
     * Create a new cart with the specified enrollment
     * and display the cart.
     */
    public function bill(Enrollment $enrollment)
    {
        // if the enrollment has already been invoiced, continue with the same invoice
        if ($enrollment->invoice) {
            Alert::success(__('This enrollment has already been invoiced'))->flash();
            return redirect()->back();
        }

        // otherwise create a new one.
        Log::info('User # '.backpack_user()->id.' is generating a invoice');

        // build an array with products to include
        $products = [];

        foreach (Fee::where('default', 1)->get() as $fee)
        {
            array_push($products, $fee);
        }

        array_push($products, $enrollment);

        if ($enrollment->course->books->count() > 0)
        {
            array_push($products, $enrollment->course->books);
        }


        // build an array with all contact data
        $clients = [];

        array_push($clients, [
            'name' => $enrollment->student_name,
            'email' => $enrollment->student_email,
            'idnumber' => $enrollment->student->idnumber,
        ]);

        foreach ($enrollment->student->contacts as $client) {
            array_push($clients, $client);
        }

        return view('carts.show', [
            'enrollment' => $enrollment,
            'products' => $products,
            'invoicetypes' => InvoiceType::all(),
            'clients' => $clients,
            'availableBooks' => Book::all(),
            'availableFees' => Fee::all(),
            'availableDiscounts' => Discount::all(),
            'availablePaymentMethods' => Paymentmethod::all(),
        ]);
    }

    public function markaspaid(Enrollment $enrollment)
    {
        $enrollment->markAsPaid();

        return redirect()->back();
    }

    public function markasunpaid(Enrollment $enrollment)
    {
        $enrollment->update(['status_id' => 1]);

        return redirect()->back();
    }

    public function savePrice(Enrollment $enrollment, Request $request)
    {
        $request->validate(['price' => 'required|numeric']);

        $enrollment->update(['total_price' => $request->price]);

        return $enrollment->fresh();
    }
}
