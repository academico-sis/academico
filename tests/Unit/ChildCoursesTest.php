<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Period;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ChildCoursesTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setSharedVariables();
        $this->seed('TestSeeder');
    }

    public function test_that_an_enrollment_in_a_course_with_children_also_create_child_enrollments()
    {
        $parentCourse = factory(Course::class)->create();
        $childCourse = factory(Course::class)->create(['parent_course_id' => $parentCourse->id]);
        $student = factory(Student::class)->create();

        $student->enroll($parentCourse);

        $this->assertEquals(1, $parentCourse->enrollments()->where('student_id', $student->id)->count());
        $this->assertEquals(1, $childCourse->enrollments()->where('student_id', $student->id)->count());
    }

    public function test_that_billing_a_parent_enrollment_also_bills_child_enrollments()
    {
        $parentCourse = factory(Course::class)->create();
        $childCourse = factory(Course::class)->create(['parent_course_id' => $parentCourse->id]);
        $student = factory(Student::class)->create();
        $student->enroll($parentCourse);
        $enrollmentInParentCourse = Enrollment::where('student_id', $student->id)->where('course_id', $parentCourse->id)->first();
        $enrollmentInChildCourse = Enrollment::where('student_id', $student->id)->where('course_id', $childCourse->id)->first();
        $this->assertFalse($enrollmentInParentCourse->isPaid());
        $this->assertFalse($enrollmentInChildCourse->isPaid());

        $enrollmentInParentCourse->markAsPaid();

        $this->assertTrue($enrollmentInParentCourse->fresh()->isPaid());
        $this->assertTrue($enrollmentInChildCourse->fresh()->isPaid());
    }

    public function test_that_a_parent_enrollment_only_appears_once_in_students_lists()
    {
        $period = factory(Period::class)->create();
        $parentCourse = factory(Course::class)->create(['period_id' => $period->id]);
        factory(Course::class)->create(['parent_course_id' => $parentCourse->id, 'period_id' => $period->id]);
        $student = factory(Student::class)->create();

        $student->enroll($parentCourse);

        $this->assertEquals(1, $period->studentCount());
    }
}
