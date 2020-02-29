<?php

namespace Tests\Unit\Http\Controllers;

use Tests\TestCase;
use App\Models\Course;
use App\Models\Student;
use App\Models\Attendance;
use App\Models\Enrollment;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceControllerTest extends TestCase
{

    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('TestSeeder');
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testCourseIncompleteAttendanceCount()
    {
        // given a course with some past classes
        $course = factory(Course::class)->create();
        $course->times()->create(['day' => 1, 'start' => '09:00:00', 'end' => '17:00:00']);
        $course->times()->create(['day' => 2, 'start' => '09:00:00', 'end' => '17:00:00']);

        // and a student enrolled in the course
        $student = factory(Student::class)->create();
        
        // We have to manually create the enrollment to prevent automatic attendance record creation (see next test)
        DB::table('enrollments')->insert([
            'course_id' => $course->id,
            'student_id' => $student->id
        ]);

        // the course attendance should miss 2 attendance records for this student
        $this->assertEquals(2, count($course->pending_attendance));
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function testLateEnrollmentAttendanceException()
    {
        // given a course with some past classes
        $course = factory(Course::class)->create();
        $course->times()->create(['day' => 1, 'start' => '09:00:00', 'end' => '17:00:00']);
        $course->times()->create(['day' => 2, 'start' => '09:00:00', 'end' => '17:00:00']);

        // when a student enrolled in the course
        $student = factory(Student::class)->create();
        $student->enroll($course);
        
        // the course attendance records are automatically created for them for any class before the enrollment date
        $this->assertEquals(0, count($course->pending_attendance));
    }
}
