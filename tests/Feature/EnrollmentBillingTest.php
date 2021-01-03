<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentBillingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('TestSeeder');
    }

    public function test_pending_enrollment_checkout()
    {
        // when an enrollment is first created, the status is pending
        $student = factory(Student::class)->create();
        $course = factory(Course::class)->create();
        $student->enroll($course);
        $enrollment = Enrollment::where('student_id', $student->id)->where('course_id', $course->id)->first();
        $this->assertEquals(1, $enrollment->status_id);

        // after billing the enrollment, the status is updated to 2
        $enrollment->markAsPaid();
        $this->assertEquals(2, $enrollment->status_id);
    }

    public function test_pending_childrenEnrollment_checkout()
    {
        // when an enrollment is first created, the status is pending including for children enrollments
        $student = factory(Student::class)->create();
        $course = factory(Course::class)->create();
        $childrenCourse = factory(Course::class)->create([
            'parent_course_id' => $course->id,
        ]);
        $student->enroll($course);

        $enrollment = Enrollment::where('student_id', $student->id)->where('course_id', $course->id)->first();
        $this->assertEquals(1, $enrollment->status_id);

        $childrenEnrollment = Enrollment::where('student_id', $student->id)->where('course_id', $childrenCourse->id)->first();
        $this->assertEquals(1, $childrenEnrollment->status_id);

        // after billing the enrollment, the status is updated to 2
        $enrollment->markAsPaid();
        $this->assertEquals(2, $enrollment->status_id);
        $childrenEnrollment = Enrollment::where('student_id', $student->id)->where('course_id', $childrenCourse->id)->first();
        $this->assertEquals(2, $childrenEnrollment->status_id);
    }

    /** @test */
    public function default_fees_are_billed_along_with_enrollment()
    {
        // [WIP] given several fees recorded in the DB
        // the fees with a flag of "default" will be automatically added to the cart along with an enrollment
        $this->markTestIncomplete('Test unfinished');
    }
}
