<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreEnrollmentRequest;
use App\Models\Attendance;
use App\Models\Book;
use App\Models\Comment;
use App\Models\Course;
use App\Models\Discount;
use App\Models\Enrollment;
use App\Models\Fee;
use App\Models\Paymentmethod;
use App\Models\PreInvoice;
use App\Models\PreInvoiceDetail;
use App\Models\Student;
use App\Traits\PeriodSelection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class EnrollmentController extends Controller
{
    use PeriodSelection;

    public function __construct()
    {
        parent::__construct();
        $this->middleware('permission:enrollments.create', ['except' => 'store']);
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
        $fees = Fee::first()->get();

        $availableBooks = Book::all();
        $availableFees = Fee::all();
        $availableDiscounts = Discount::all();
        $contactData = $enrollment->student->contacts;
        $availablePaymentMethods = Paymentmethod::all();

        return view('carts.show', compact('enrollments', 'fees', 'books', 'availableBooks', 'availableFees', 'availableDiscounts', 'contactData', 'availablePaymentMethods'));
    }

    /** this method is temporary. It serves as a shortcut until the invoicing system is in place */
    public function quickBill(Enrollment $enrollment)
    {
        Log::info(backpack_user()->firstname.' is (quick) billing enrollment '.$enrollment->id);

        return view('invoices.create', compact('enrollment'));
    }

    public function quickInvoice(Request $request)
    {

        // get the invoice number and the comment
        $invoice_number = $request->input('invoice_number');
        $comment = $request->input('comment');
        $enrollment = Enrollment::findOrFail($request->input('enrollment_id'));

        if ($enrollment->status_id != 1) {
            abort(422, 'Esta matricula no esta pendiente');
        }

        // create a preinvoice with this enrollment
        $preinvoice = new PreInvoice;
        $preinvoice->client_name = $enrollment->student->name;
        $preinvoice->client_idnumber = $enrollment->student->idnumber;
        $preinvoice->client_address = $enrollment->student->address;
        $preinvoice->client_email = $enrollment->student->email;
        $preinvoice->total_price = $enrollment->product->price ?? 0;
        $preinvoice->save();

        // generate a preinvoice product (detail)
        $detail = new PreInvoiceDetail;
        $detail->pre_invoice_id = $preinvoice->id;
        $detail->product_name = $enrollment->product->name ?? 'curso';
        $detail->price = $enrollment->product->price ?? 0;
        $detail->save();

        // change the enrollment status
        $enrollment->status_id = 2;
        $enrollment->save();

        // and status of children enrollments

        if ($enrollment->childrenEnrollments->count() > 0) {
            foreach ($enrollment->childrenEnrollments as $child_enrollment) {
                $child_enrollment->status_id = 2;
                $child_enrollment->save();
            }
        }

        if ($comment !== null) {
            Comment::create([
                'commentable_id' => $enrollment->id,
                'commentable_type' => Enrollment::class,
                'body' => $comment,
                'author_id' => backpack_user()->id,
            ]);
        }

        if ($invoice_number !== null) {
            $preinvoice->invoice_number = $invoice_number;
            $preinvoice->save();
        }

        // link the enrollment and the preinvoice
        $preinvoice->enrollments()->attach($enrollment);

        // return the preinvoice id for redirect
        // todo allow comments on that page.
        return redirect("/enrollment/$enrollment->id/show");
        //return $preinvoice->id;
    }
}
