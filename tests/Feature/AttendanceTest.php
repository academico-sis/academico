<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceTest extends TestCase
{
    public $course;
    public $attributes;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestSeeder');

        $teacher = factory(Teacher::class)->create();

        // Arrange a course with some events, and students
        $this->course = factory(Course::class)->create([
            'teacher_id' => $teacher->id,
        ]); // todo allow coursetime creation from the course factory

        $teacher->user->givePermissionTo('attendance.edit');
        $teacher->user->givePermissionTo('attendance.view');

        // add a coursetime, which will create events
        $this->course->times()->create([
            'day' => 3,
            'start' => '15:45:00',
            'end' => '17:45:00',
        ]);

        for ($i = 0; $i < 4; $i++) {
            $student = factory(Student::class)->create();
            $student->enroll($this->course);
        }

        $this->attributes = [
            'student_id' => $this->course->enrollments()->first()->student->id,
            'event_id' => $this->course->events->first()->id,
            'attendance_type_id' => 1,
        ];
    }

    private function recordAttendanceByTeacher($attributes)
    {
        \Auth::guard(backpack_guard_name())->login($this->course->teacher->user);

        // when the teacher posts a new attendance record
        $this->json('POST', route('storeAttendance'), $attributes);
    }

    /** @test */
    public function authorized_users_can_take_attendance()
    {
        $this->recordAttendanceByTeacher($this->attributes);

        // it is persisted to the DB
        $this->assertDatabaseHas('attendances', $this->attributes);
    }

    /** @test
     * guests can not use the post route
     */
    public function unauthorized_users_cannot_take_attendance()
    {
        $student = $this->course->enrollments()->first()->student;

        $attributes = [
            'student_id' => $student->id,
            'event_id' => $this->course->events->first()->id,
            'attendance_type_id' => 1,
        ];

        $this->json('POST', route('storeAttendance'), $attributes);
        $this->assertDatabaseMissing('attendances', $attributes);

        // teacher with no permission

        $user = factory(Teacher::class)->create()->user;
        \Auth::guard(backpack_guard_name())->login($user);

        $this->json('POST', route('storeAttendance'), $attributes)->assertStatus(403);
        $this->assertDatabaseMissing('attendances', $attributes);

        // teacher with permission but for a class that is not their own
        $user = factory(Teacher::class)->create()->user;

        \Auth::guard(backpack_guard_name())->login($user);

        $this->json('POST', route('storeAttendance'), $attributes)->assertStatus(403);
        $this->assertDatabaseMissing('attendances', $attributes);
    }

    public function test_attendance_overview_per_course()
    {
        $this->markTestIncomplete();
        // the teacher can view the attendance for their courses
        // but not for other courses
    }

    public function test_absence_monitoring()
    {
        $student = $this->course->enrollments()->first()->student;

        // when an absence record is created, the student number of absences is incremented.

        $this->recordAttendanceByTeacher([
            'student_id' => $student->id,
            'event_id' => $this->course->events->first()->id,
            'attendance_type_id' => 4,
        ]);

        $absencesCountBefore = $student->periodAbsences($this->course->period)->count();

        $this->recordAttendanceByTeacher([
            'student_id' => $student->id,
            'event_id' => $this->course->events->last()->id,
            'attendance_type_id' => 4,
        ]);

        $this->recordAttendanceByTeacher([
            'student_id' => $student->id,
            'event_id' => $this->course->events->get(2)->id,
            'attendance_type_id' => 1,
        ]);

        $absencesCountAfter = $student->periodAbsences($this->course->period)->count();

        $this->assertTrue($absencesCountAfter == $absencesCountBefore + 1);
    }
}
