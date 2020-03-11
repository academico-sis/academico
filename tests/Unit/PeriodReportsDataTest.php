<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Course;
use App\Models\Period;
use App\Models\Student;
use App\Models\Enrollment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PeriodReportsDataTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Period->real_enrollments must return the count of pending enrollments in the period
     */
    public function testRealEnrollmentsCountIncludesPendingEnrollments()
    {
        $period = Period::get_default_period();

        $total = rand(2,10);
        for ($i=0; $i < $total; $i++) { 
            // given an enrollment in the current period
            $course = factory(Course::class)->create(['period_id' => $period->id]);
            $student = factory(Student::class)->create();
            $enrollment = $student->enroll($course);
        }

        $countAfterEnrollment = $period->real_enrollments->count();
        $this->assertEquals($total, $countAfterEnrollment);
    }

    /**
     * Period->real_enrollments must return the count of paid enrollments in the period
     */
    public function testRealEnrollmentsCountIncludesPaidEnrollments()
    {
        $period = Period::get_default_period();

        // given an enrollment in the current period
        $total = rand(2,10);
        for ($i=0; $i < $total; $i++) { 
            $course = factory(Course::class)->create(['period_id' => $period->id]);
            $student = factory(Student::class)->create();
            $enrollment = Enrollment::find($student->enroll($course));
            $enrollment->markAsPaid();
        }
        
        $countAfterEnrollment = $period->real_enrollments->count();
        $this->assertEquals($total, $countAfterEnrollment);
    }

    /**
     * Period->real_enrollments must return the count of paid or pending enrollments in the period
     * without deleted enrollments
     */
    public function testRealEnrollmentsCountExcludesDeletedEnrollments()
    {
        $total = rand(2,10);
        for ($i=0; $i < $total; $i++) { 
            // given an enrollment in the current period
            $period = Period::get_default_period();
            $course = factory(Course::class)->create(['period_id' => $period->id]);
            $student = factory(Student::class)->create();
            $enrollment = Enrollment::find($student->enroll($course));
        }
        
        $enrollment->cancel();
        $countAfterEnrollment = $period->real_enrollments->count();
        $this->assertEquals(($total - 1), $countAfterEnrollment);
    }

    /**
     * Period->real_enrollments must return the count of paid or pending enrollments in the period
     * without children enrollments
     */
    public function testRealEnrollmentsCountExcludesChildrenEnrollments()
    {
        // given an enrollment in the current period
        $period = Period::get_default_period();

        // if the course has children
        $parentCourse = factory(Course::class)->create();
        $childrenCourse = factory(Course::class)->create(['parent_course_id' => $parentCourse->id]);
        $student = factory(Student::class)->create();
        $enrollment = $student->enroll($parentCourse);
        $this->assertEquals(2, $student->enrollments->count());
        
        // the enrollment is only counted once
        $countAfterEnrollment = $period->real_enrollments->count();
        $this->assertEquals(1, $countAfterEnrollment);
    }

    /**
     * Period->real_enrollments must count paid or pending enrollments in children courses,
     * provided that this enrollment has no parent
     */
    public function testRealEnrollmentsCountIncludesEnrollmentsInChildrenCourses()
    {
        // given an enrollment in the current period
        $period = Period::get_default_period();

        // if the course has children
        $parentCourse = factory(Course::class)->create();
        $childrenCourse = factory(Course::class)->create(['parent_course_id' => $parentCourse->id]);
        $student = factory(Student::class)->create();
        $enrollment = $student->enroll($childrenCourse);
        
        // the enrollment is counted once
        $countAfterEnrollment = $period->real_enrollments->count();
        $this->assertEquals(1, $countAfterEnrollment);
    }
}
