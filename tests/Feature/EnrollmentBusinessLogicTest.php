<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceType;
use App\Models\Payment;
use App\Models\Scholarship;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('enrollment_status_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Pending'])],
            ['id' => 2, 'name' => json_encode(['fr' => 'Paid'])],
            ['id' => 3, 'name' => json_encode(['fr' => 'Cancelled'])],
        ]);

        \DB::table('attendance_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Present'])],
            ['id' => 2, 'name' => json_encode(['fr' => 'Late'])],
            ['id' => 3, 'name' => json_encode(['fr' => 'Absent - Justified'])],
            ['id' => 4, 'name' => json_encode(['fr' => 'Absent - Unjustified'])],
        ]);
    }

    public function test_add_scholarship_marks_paid_when_config_enabled(): void
    {
        config()->set('invoicing.adding_scholarship_marks_as_paid', true);

        $enrollment = Enrollment::factory()->create(['status_id' => 1]);
        $scholarship = Scholarship::factory()->create();

        $enrollment->addScholarship($scholarship);

        $this->assertEquals(2, $enrollment->fresh()->status_id);
        $this->assertTrue($enrollment->scholarships->contains($scholarship));
    }

    public function test_add_scholarship_does_not_mark_paid_when_config_disabled(): void
    {
        config()->set('invoicing.adding_scholarship_marks_as_paid', false);

        $enrollment = Enrollment::factory()->create(['status_id' => 1]);
        $scholarship = Scholarship::factory()->create();

        $enrollment->addScholarship($scholarship);

        $this->assertEquals(1, $enrollment->fresh()->status_id);
        $this->assertTrue($enrollment->scholarships->contains($scholarship));
    }

    public function test_remove_scholarship_marks_unpaid_when_config_enabled(): void
    {
        config()->set('invoicing.adding_scholarship_marks_as_paid', true);

        $enrollment = Enrollment::factory()->create(['status_id' => 2]);
        $scholarship = Scholarship::factory()->create();
        $enrollment->scholarships()->attach($scholarship);

        $enrollment->removeScholarship($scholarship);

        $this->assertEquals(1, $enrollment->fresh()->status_id);
        $this->assertFalse($enrollment->fresh()->scholarships->contains($scholarship));
    }

    public function test_remove_scholarship_only_detaches_when_config_disabled(): void
    {
        config()->set('invoicing.adding_scholarship_marks_as_paid', false);

        $enrollment = Enrollment::factory()->create(['status_id' => 2]);
        $scholarship = Scholarship::factory()->create();
        $enrollment->scholarships()->attach($scholarship);

        $enrollment->removeScholarship($scholarship);

        $this->assertEquals(2, $enrollment->fresh()->status_id);
        $this->assertFalse($enrollment->fresh()->scholarships->contains($scholarship));
    }

    public function test_is_paid_returns_true_for_status_2(): void
    {
        $enrollment = Enrollment::factory()->create(['status_id' => 2]);

        $this->assertTrue($enrollment->isPaid());
    }

    public function test_is_paid_returns_false_for_status_1(): void
    {
        $enrollment = Enrollment::factory()->create(['status_id' => 1]);

        $this->assertFalse($enrollment->isPaid());
    }

    public function test_pending_scope_returns_parent_pending_only(): void
    {
        $course = Course::factory()->create();
        $student = Student::factory()->create();

        $pendingEnrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        $paidEnrollment = Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $course->id,
            'status_id' => 2,
        ]);

        $childEnrollment = Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $course->id,
            'status_id' => 1,
            'parent_id' => $pendingEnrollment->id,
        ]);

        $pending = Enrollment::query()->pending();

        $this->assertTrue($pending->contains('id', $pendingEnrollment->id));
        $this->assertFalse($pending->contains('id', $paidEnrollment->id));
        $this->assertFalse($pending->contains('id', $childEnrollment->id));
    }

    public function test_noresult_scope_returns_enrollments_without_result(): void
    {
        $enrollmentWithoutResult = Enrollment::factory()->create();
        $enrollmentWithResult = Enrollment::factory()->create();

        // Create a result for one enrollment
        \App\Models\Result::create([
            'enrollment_id' => $enrollmentWithResult->id,
            'result_type_id' => \App\Models\ResultType::factory()->create()->id,
        ]);

        $noresult = Enrollment::noresult()->pluck('id');

        $this->assertTrue($noresult->contains($enrollmentWithoutResult->id));
        $this->assertFalse($noresult->contains($enrollmentWithResult->id));
    }

    public function test_period_scope_filters_by_course_period(): void
    {
        $course1 = Course::factory()->create();
        $course2 = Course::factory()->create();

        $enrollment1 = Enrollment::factory()->create(['course_id' => $course1->id]);
        $enrollment2 = Enrollment::factory()->create(['course_id' => $course2->id]);

        $filtered = Enrollment::period($course1->period_id)->pluck('id');

        $this->assertTrue($filtered->contains($enrollment1->id));
        $this->assertFalse($filtered->contains($enrollment2->id));
    }

    public function test_attendance_ratio_calculation(): void
    {
        $course = Course::factory()->create();
        $student = Student::factory()->create();

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        // Create events and attendance records
        $events = [];
        for ($i = 0; $i < 4; $i++) {
            $events[] = Event::factory()->create([
                'course_id' => $course->id,
                'start' => now()->addDays($i + 10)->toDateTimeString(),
                'end' => now()->addDays($i + 10)->addHour()->toDateTimeString(),
            ]);
        }

        // Present, Late, Justified Absence, Unjustified Absence
        Attendance::create(['student_id' => $student->id, 'event_id' => $events[0]->id, 'attendance_type_id' => 1]);
        Attendance::create(['student_id' => $student->id, 'event_id' => $events[1]->id, 'attendance_type_id' => 2]);
        Attendance::create(['student_id' => $student->id, 'event_id' => $events[2]->id, 'attendance_type_id' => 3]);
        Attendance::create(['student_id' => $student->id, 'event_id' => $events[3]->id, 'attendance_type_id' => 4]);

        // Ratio = (1 present + 0.75 late) / 4 total = 1.75/4 = 43.75 → 44 (rounded)
        $this->assertEquals(44, $enrollment->fresh()->attendance_ratio);
    }

    public function test_absence_count_sums_justified_and_unjustified(): void
    {
        $course = Course::factory()->create();
        $student = Student::factory()->create();

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        $event1 = Event::factory()->create([
            'course_id' => $course->id,
            'start' => now()->addDays(10)->toDateTimeString(),
            'end' => now()->addDays(10)->addHour()->toDateTimeString(),
        ]);
        $event2 = Event::factory()->create([
            'course_id' => $course->id,
            'start' => now()->addDays(11)->toDateTimeString(),
            'end' => now()->addDays(11)->addHour()->toDateTimeString(),
        ]);
        $event3 = Event::factory()->create([
            'course_id' => $course->id,
            'start' => now()->addDays(12)->toDateTimeString(),
            'end' => now()->addDays(12)->addHour()->toDateTimeString(),
        ]);

        Attendance::create(['student_id' => $student->id, 'event_id' => $event1->id, 'attendance_type_id' => 1]); // Present
        Attendance::create(['student_id' => $student->id, 'event_id' => $event2->id, 'attendance_type_id' => 3]); // Justified
        Attendance::create(['student_id' => $student->id, 'event_id' => $event3->id, 'attendance_type_id' => 4]); // Unjustified

        $this->assertEquals(2, $enrollment->fresh()->absence_count);
    }

    public function test_price_uses_student_price_category_when_enabled(): void
    {
        config()->set('invoicing.price_categories_enabled', true);

        $course = Course::factory()->create(['price' => 100]);
        // Set price_b on the course
        $course->update(['price_b' => 80]);

        $student = Student::factory()->create(['price_category' => 'price_b']);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        $this->assertEquals(80, $enrollment->price);
    }

    public function test_price_falls_back_to_course_price(): void
    {
        config()->set('invoicing.price_categories_enabled', false);

        $course = Course::factory()->create(['price' => 150]);
        $enrollment = Enrollment::factory()->create([
            'course_id' => $course->id,
        ]);

        $this->assertEquals(150, $enrollment->price);
    }

    public function test_balance_computes_total_minus_paid(): void
    {
        config()->set('invoicing.invoices_contain_enrollments_only', true);

        $course = Course::factory()->create(['price' => 100]);
        $enrollment = Enrollment::factory()->create([
            'course_id' => $course->id,
            'total_price' => 100,
        ]);

        $invoiceType = InvoiceType::factory()->create();
        $invoice = Invoice::factory()->create(['invoice_type_id' => $invoiceType->id]);
        InvoiceDetail::create([
            'invoice_id' => $invoice->id,
            'product_name' => 'Enrollment',
            'product_code' => 'E',
            'product_id' => $enrollment->id,
            'product_type' => Enrollment::class,
            'price' => 100,
        ]);
        Payment::factory()->create(['invoice_id' => $invoice->id, 'value' => 60]);

        $balance = $enrollment->fresh()->balance;

        $this->assertEquals(100 - 60, $balance);
    }

    public function test_balance_aborts_when_config_disabled(): void
    {
        config()->set('invoicing.invoices_contain_enrollments_only', false);

        $enrollment = Enrollment::factory()->create();

        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        $enrollment->balance;
    }

    public function test_total_paid_price_sums_invoice_payments(): void
    {
        $enrollment = Enrollment::factory()->create();
        $invoiceType = InvoiceType::factory()->create();

        $invoice = Invoice::factory()->create(['invoice_type_id' => $invoiceType->id]);
        InvoiceDetail::create([
            'invoice_id' => $invoice->id,
            'product_name' => 'Enrollment',
            'product_code' => 'E',
            'product_id' => $enrollment->id,
            'product_type' => Enrollment::class,
            'price' => 100,
        ]);

        Payment::factory()->create(['invoice_id' => $invoice->id, 'value' => 40]);
        Payment::factory()->create(['invoice_id' => $invoice->id, 'value' => 30]);

        $this->assertEquals(70, $enrollment->fresh()->total_paid_price);
    }

    public function test_total_paid_price_returns_zero_with_no_invoices(): void
    {
        $enrollment = Enrollment::factory()->create();

        $this->assertEquals(0, $enrollment->total_paid_price);
    }

    public function test_has_book_for_course_returns_ok(): void
    {
        $course = Course::factory()->create();
        $student = Student::factory()->create();
        $book = \App\Models\Book::factory()->create();

        $course->books()->attach($book);
        $student->books()->attach($book, [
            'code' => 'ABC',
            'status_id' => 1,
            'expiry_date' => now()->addYear()->format('Y-m-d'),
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        $this->assertEquals('OK', $enrollment->fresh()->has_book_for_course);
    }

    public function test_has_book_for_course_returns_false_when_missing(): void
    {
        $course = Course::factory()->create();
        $student = Student::factory()->create();
        $book = \App\Models\Book::factory()->create();

        $course->books()->attach($book);
        // Student does NOT have this book

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        $this->assertFalse($enrollment->fresh()->has_book_for_course);
    }

    public function test_has_book_for_course_returns_exp_when_expired(): void
    {
        $course = Course::factory()->create();
        $student = Student::factory()->create();
        $book = \App\Models\Book::factory()->create();

        $course->books()->attach($book);
        $student->books()->attach($book, [
            'code' => 'ABC',
            'status_id' => 1,
            'expiry_date' => now()->subDay()->format('Y-m-d'),
        ]);

        $enrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        $this->assertEquals('EXP', $enrollment->fresh()->has_book_for_course);
    }

    public function test_has_book_for_course_returns_null_when_no_books(): void
    {
        $course = Course::factory()->create();
        $enrollment = Enrollment::factory()->create(['course_id' => $course->id]);

        $this->assertNull($enrollment->fresh()->has_book_for_course);
    }
}
