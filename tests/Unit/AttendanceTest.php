<?php

namespace Tests\Unit;

use App\Mail\PendingAttendanceReminder;
use App\Models\Attendance;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Period;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('TestSeeder');
    }

    /**
     * Display the number of events with missing attendance for a course.
     *
     * @return void
     */
    public function testCourseIncompleteAttendanceCount()
    {
        // given a course with some past classes
        $course = factory(Course::class)->create([
            'start_date' => date('Y-m-d', strtotime('-6 days')),
            'end_date' => date('Y-m-d'),
        ]);
        $course->times()->create(['day' => 1, 'start' => '09:00:00', 'end' => '17:00:00']);
        $course->times()->create(['day' => 2, 'start' => '09:00:00', 'end' => '17:00:00']);

        // and a student enrolled in the course
        $student = factory(Student::class)->create();

        // We have to manually create the enrollment to prevent automatic attendance record creation (see next test)
        DB::table('enrollments')->insert([
            'course_id' => $course->id,
            'student_id' => $student->id,
        ]);

        // the course attendance should miss 2 attendance records for this student
        $this->assertCount(2, $course->pending_attendance);
        $this->assertEquals(2, $course->eventsWithExpectedAttendance->count());
    }

    /**
     * Enrolling a student in a course automatically creates attendance records for any past event in the course.
     * This is to prevent incomplete attendance in case of late enrollments.
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
        $this->assertCount(0, $course->pending_attendance);
    }

    /**
     * Send email reminders to all teachers who have classes with incomplete attendance records.
     */
    public function testRemindPendingAttendance()
    {
        Mail::fake();
        // given a course with incomplete attendance
        $teacher = factory(Teacher::class)->create();
        $course = factory(Course::class)->create(['teacher_id' => $teacher->id]);
        $course->times()->create(['day' => 1, 'start' => '09:00:00', 'end' => '17:00:00']);
        $course->times()->create(['day' => 2, 'start' => '09:00:00', 'end' => '17:00:00']);

        // and a student enrolled in the course
        $student = factory(Student::class)->create();

        // We have to manually create the enrollment to prevent automatic attendance record creation (see next test)
        DB::table('enrollments')->insert([
            'course_id' => $course->id,
            'student_id' => $student->id,
        ]);

        // a notification email is sent to the teacher of this event
        (new Attendance)->remindPendingAttendance();

        Mail::assertQueued(PendingAttendanceReminder::class);
    }

    /** Absence count per student for the selected period */
    public function test_get_absence_count_per_student()
    {
        Mail::fake();
        // given a course with incomplete attendance
        $teacher = factory(Teacher::class)->create();
        $course = factory(Course::class)->create(['teacher_id' => $teacher->id]);

        // and a student enrolled in the course
        $student = factory(Student::class)->create();

        // We have to manually create the enrollment to prevent automatic attendance record creation (see next test)
        DB::table('enrollments')->insert([
            'course_id' => $course->id,
            'student_id' => $student->id,
        ]);

        $event = $course->events()->create([
            'start' => date('Y-m-d', strtotime('-2 days')),
            'end' => date('Y-m-d', strtotime('-1 days')),
            'name' => 'test event 1',
            'teacher_id' => $teacher->fresh()->id,
        ]);

        Attendance::create([
            'student_id' => $student->id,
            'event_id' => $event->id,
            'attendance_type_id' => 4,
        ]);

        $event = $course->events()->create([
            'start' => date('Y-m-d', strtotime('-3 days')),
            'end' => date('Y-m-d', strtotime('-2 days')),
            'name' => 'test event 2',
            'teacher_id' => $teacher->id,
        ]);

        Attendance::create([
            'student_id' => $student->id,
            'event_id' => $event->id,
            'attendance_type_id' => 3,
        ]);

        // the absence count for this student should be two
        $absences = (new Attendance)->get_absence_count_per_student(Period::get_default_period());
        $this->assertEquals(2, $absences->first()->count());
    }

    /**
     * Return events with incomplete attendance. This is shown on the dashboard.
     */
    public function test_get_pending_attendance()
    {
        // given a course with incomplete attendance
        $teacher = factory(Teacher::class)->create();
        $course = factory(Course::class)->create(['teacher_id' => $teacher->id]);

        // and a student enrolled in the course
        $student = factory(Student::class)->create();

        // We have to manually create the enrollment to prevent automatic attendance record creation (see next test)
        DB::table('enrollments')->insert([
            'course_id' => $course->id,
            'student_id' => $student->id,
        ]);

        $event1 = $course->events()->create([
            'start' => date('Y-m-d', strtotime('-2 days')),
            'end' => date('Y-m-d', strtotime('-1 days')),
            'name' => 'test event 1',
            'teacher_id' => $teacher->id,
        ]);

        $event2 = $course->events()->create([
            'start' => date('Y-m-d', strtotime('-3 days')),
            'end' => date('Y-m-d', strtotime('-2 days')),
            'name' => 'test event 2',
            'teacher_id' => $teacher->id,
        ]);

        $coursesWithPendingAttendanceCount = Period::get_default_period()->courses_with_pending_attendance;

        $this->assertEquals(1, $coursesWithPendingAttendanceCount);

        Attendance::create([
            'student_id' => $student->id,
            'event_id' => $event2->id,
            'attendance_type_id' => 2,
        ]);

        Attendance::create([
            'student_id' => $student->id,
            'event_id' => $event1->id,
            'attendance_type_id' => 3,
        ]);

        $coursesWithPendingAttendanceCount = Period::get_default_period()->courses_with_pending_attendance;

        $this->assertEquals(0, $coursesWithPendingAttendanceCount);
    }
}
