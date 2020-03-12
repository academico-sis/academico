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

    /** Period->getStudentsCountAttribute
     * Return the number of unique students for the period
     */
    public function testPeriodStudentsCountAttribute()
    {
        $period = Period::get_default_period();
        $course = factory(Course::class)->create(['period_id' => $period->id]);

        // given a number of students
        $studentsEnrolledOnce = rand(2,12);
        
        for ($i=0; $i < $studentsEnrolledOnce; $i++) {
            $student = factory(Student::class)->create();
            $enrollment = Enrollment::find($student->enroll($course));
        }

        // If some students are enrolled in two courses
        $course2 = factory(Course::class)->create(['period_id' => $period->id]);
        $studentsEnrolledTwice = rand(2,12);
        
        for ($i=0; $i < $studentsEnrolledTwice; $i++) {
            $student = factory(Student::class)->create();
            $enrollment = Enrollment::find($student->enroll($course));
            $enrollment = Enrollment::find($student->enroll($course2));
        }

        // they are only counted once in the total
        $this->assertEquals(($studentsEnrolledOnce + $studentsEnrolledTwice), $period->students_count);
    }

    /** getAcquisitionRateAttribute
     * the ratio of students from period P-1 who are still here in period P
     * TODO make the numbers random.
     * The attribute should return the raw value and another getter should be used to format it as %
     */

     public function testAcquisitionRateAttribute()
     {
        // given a number of students enrolled in period P-1
        $period1 = Period::get_default_period();
        $courseForPeriod1 = factory(Course::class)->create(['period_id' => $period1->id]);
        $numberOfstudentsInPeriod1 = 20;
        
        for ($i=0; $i < $numberOfstudentsInPeriod1; $i++) {
            $student = factory(Student::class)->create();
            $enrollment = Enrollment::find($student->enroll($courseForPeriod1));
        }

        $studentsInPeriod1 = Student::all();

        // if some of these students are also enrolled in period P
        $period2 = Period::create([
            'name' => 'period 2',
            'start' => date('Y-m-d', strtotime("+1 day")),
            'end' => date('Y-m-d', strtotime("+90 days")),
            'year_id' => 1
        ]);

        $courseForPeriod2 = factory(Course::class)->create(['period_id' => $period2->id]);
        $studentsInPeriod2 = $studentsInPeriod1->random($numberOfstudentsInPeriod1/4);
        
        foreach ($studentsInPeriod2 as $student) {
            $enrollment = Enrollment::find($student->enroll($courseForPeriod2));
        }

        // the acquisition rate for period P should be the ratio between period 1 and 2
        $this->assertEquals("25.0%", $period2->acquisition_rate);
     }
}
