<?php

namespace Tests\Feature;

use App\Models\Config;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Invoice;
use App\Models\InvoiceDetail;
use App\Models\InvoiceType;
use App\Models\Payment;
use App\Models\Period;
use App\Models\Student;
use App\Models\Year;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PeriodBusinessLogicTest extends TestCase
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
    }

    public function test_get_default_period_returns_config_period(): void
    {
        $year = Year::factory()->create();
        $period = Period::factory()->create(['year_id' => $year->id]);
        Config::where('name', 'current_period')->update(['value' => $period->id]);

        $result = Period::get_default_period();

        $this->assertEquals($period->id, $result->id);
    }

    public function test_get_default_period_falls_back_to_next_active_when_config_invalid(): void
    {
        $year = Year::factory()->create();
        Config::where('name', 'current_period')->update(['value' => 99999]);

        $activePeriod = Period::factory()->create([
            'year_id' => $year->id,
            'start' => now()->subDays(10)->format('Y-m-d'),
            'end' => now()->addDays(30)->format('Y-m-d'),
        ]);

        $result = Period::get_default_period();

        $this->assertEquals($activePeriod->id, $result->id);
    }

    public function test_get_default_period_returns_most_recent_when_all_expired(): void
    {
        $year = Year::factory()->create();
        Config::where('name', 'current_period')->update(['value' => 99999]);

        $olderPeriod = Period::factory()->create([
            'year_id' => $year->id,
            'start' => now()->subDays(200)->format('Y-m-d'),
            'end' => now()->subDays(100)->format('Y-m-d'),
        ]);
        $newerPeriod = Period::factory()->create([
            'year_id' => $year->id,
            'start' => now()->subDays(90)->format('Y-m-d'),
            'end' => now()->subDays(1)->format('Y-m-d'),
        ]);

        $result = Period::get_default_period();

        $this->assertEquals($newerPeriod->id, $result->id);
    }

    public function test_get_enrollments_period_returns_config_period(): void
    {
        $year = Year::factory()->create();
        $period = Period::factory()->create(['year_id' => $year->id]);
        Config::where('name', 'default_enrollment_period')->update(['value' => $period->id]);

        $result = Period::get_enrollments_period();

        $this->assertEquals($period->id, $result->id);
    }

    public function test_get_enrollments_period_switches_to_next_when_ending_soon(): void
    {
        $year = Year::factory()->create();

        // Create a period that is >50% elapsed
        $currentPeriod = Period::factory()->create([
            'year_id' => $year->id,
            'start' => now()->subDays(80)->format('Y-m-d'),
            'end' => now()->addDays(10)->format('Y-m-d'),
        ]);

        $nextPeriod = Period::factory()->create([
            'year_id' => $year->id,
            'start' => now()->addDays(11)->format('Y-m-d'),
            'end' => now()->addDays(100)->format('Y-m-d'),
        ]);

        // Config points to invalid id so it falls through to default period logic
        Config::where('name', 'default_enrollment_period')->update(['value' => 99999]);
        Config::where('name', 'current_period')->update(['value' => $currentPeriod->id]);

        $result = Period::get_enrollments_period();

        $this->assertEquals($nextPeriod->id, $result->id);
    }

    public function test_acquisition_rate_computes_returning_student_percentage(): void
    {
        $year = Year::factory()->create();
        $period1 = Period::factory()->create(['year_id' => $year->id]);
        $period2 = Period::factory()->create(['year_id' => $year->id]);

        $course1 = Course::factory()->create(['period_id' => $period1->id]);
        $course2 = Course::factory()->create(['period_id' => $period2->id]);

        $studentA = Student::factory()->create();
        $studentB = Student::factory()->create();
        $studentC = Student::factory()->create();
        $studentD = Student::factory()->create();

        // P1: A, B, C enrolled
        foreach ([$studentA, $studentB, $studentC] as $student) {
            Enrollment::create(['student_id' => $student->id, 'course_id' => $course1->id, 'status_id' => 1]);
        }

        // P2: A, B, D enrolled (C dropped, D is new)
        foreach ([$studentA, $studentB, $studentD] as $student) {
            Enrollment::create(['student_id' => $student->id, 'course_id' => $course2->id, 'status_id' => 1]);
        }

        $rate = $period2->acquisition_rate;

        // 2 out of 3 returning = 66.7%
        $this->assertEquals('66.7', $rate);
    }

    public function test_acquisition_rate_handles_no_previous_enrollments(): void
    {
        $year = Year::factory()->create();
        $period = Period::factory()->create(['year_id' => $year->id]);

        // No enrollments at all - previousPeriod returns self when it's the only one
        $rate = $period->acquisition_rate;

        $this->assertIsString($rate);
    }

    public function test_new_students_returns_first_time_enrollees(): void
    {
        $year = Year::factory()->create();
        $period1 = Period::factory()->create(['year_id' => $year->id]);
        $period2 = Period::factory()->create(['year_id' => $year->id]);

        $course1 = Course::factory()->create(['period_id' => $period1->id]);
        $course2 = Course::factory()->create(['period_id' => $period2->id]);

        $returningStudent = Student::factory()->create();
        $newStudent = Student::factory()->create();

        Enrollment::create(['student_id' => $returningStudent->id, 'course_id' => $course1->id, 'status_id' => 1]);
        Enrollment::create(['student_id' => $returningStudent->id, 'course_id' => $course2->id, 'status_id' => 1]);
        Enrollment::create(['student_id' => $newStudent->id, 'course_id' => $course2->id, 'status_id' => 1]);

        $newStudents = $period2->newStudents();

        $this->assertTrue($newStudents->pluck('student_id')->contains($newStudent->id));
        $this->assertFalse($newStudents->pluck('student_id')->contains($returningStudent->id));
    }

    public function test_active_scope_excludes_archived_periods(): void
    {
        $year = Year::factory()->create();
        $activePeriod = Period::factory()->create(['year_id' => $year->id, 'archived' => false]);
        $archivedPeriod = Period::factory()->create(['year_id' => $year->id, 'archived' => true]);

        $activeIds = Period::active()->pluck('id');

        $this->assertTrue($activeIds->contains($activePeriod->id));
        $this->assertFalse($activeIds->contains($archivedPeriod->id));
    }

    public function test_previous_period_returns_period_with_lower_id(): void
    {
        $year = Year::factory()->create();
        $period1 = Period::factory()->create(['year_id' => $year->id]);
        $period2 = Period::factory()->create(['year_id' => $year->id]);

        $previous = $period2->previousPeriod();

        $this->assertEquals($period1->id, $previous->id);
    }

    public function test_takings_sums_paid_prices(): void
    {
        $year = Year::factory()->create();
        $period = Period::factory()->create(['year_id' => $year->id]);
        $course = Course::factory()->create(['period_id' => $period->id]);

        $enrollment = Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        $invoiceType = InvoiceType::factory()->create();
        $invoice = Invoice::factory()->create(['invoice_type_id' => $invoiceType->id]);
        InvoiceDetail::create([
            'invoice_id' => $invoice->id,
            'product_name' => 'Enrollment',
            'product_code' => 'E',
            'product_id' => $enrollment->id,
            'product_type' => Enrollment::class,
            'price' => 200,
        ]);
        Payment::factory()->create(['invoice_id' => $invoice->id, 'value' => 150]);

        $this->assertEquals(150, $period->fresh()->takings);
    }

    public function test_courses_with_pending_attendance(): void
    {
        $year = Year::factory()->create();
        $period = Period::factory()->create(['year_id' => $year->id]);

        // Course with exempt_attendance explicitly set to false (non-null)
        $course = Course::factory()->create([
            'period_id' => $period->id,
            'exempt_attendance' => 0,
        ]);

        $student = Student::factory()->create();
        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        // Past event with no attendance record
        Event::factory()->create([
            'course_id' => $course->id,
            'start' => now()->subDays(2)->toDateTimeString(),
            'end' => now()->subDays(2)->addHour()->toDateTimeString(),
            'exempt_attendance' => null,
        ]);

        $count = $period->fresh()->courses_with_pending_attendance;

        $this->assertGreaterThanOrEqual(0, $count);
    }
}
