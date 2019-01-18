<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Event;
use App\Models\Period;
use App\Models\Attendance;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Carbon\Carbon;

class AttendanceTest extends TestCase
{
    use DatabaseMigrations;


    public function test_attendance_recording()
    {
        $this->seed('DatabaseSeeder');
        

        // Arrange a course with a teacher, some events, and students
        $teacher = factory(User::class)->create();
        $teacher->assignRole('teacher');
        $teacher->givePermissionTo('attendance.edit');
        $teacher->givePermissionTo('attendance.view');

        $course = factory(Course::class)->create([
            'teacher_id' => $teacher->id,
        ]);
        
        $course->events()->create([
            'start' => Carbon::parse('10 days ago'),
            'end' => Carbon::parse('10 days ago'),
            'name' => 'test event',
        ]);

        $course->events()->create([
            'start' => Carbon::parse('4 days ago'),
            'end' => Carbon::parse('4 days ago'),
            'name' => 'test event',
        ]);

        $course->events()->create([
            'start' => Carbon::parse('2 days ago'),
            'end' => Carbon::parse('2 days ago'),
            'name' => 'test event',
        ]);

        $course->events()->create([
            'start' => Carbon::parse('+2 days'),
            'end' => Carbon::parse('+2 days'),
            'name' => 'test event',
        ]);


        for ($i=0; $i < 4; $i++) { 
            $student = factory(User::class)->create();
            $student->assignRole('student');
            $student->enroll($course);
        }
        
        \Auth::guard(backpack_guard_name())->login($teacher);

        $random_student_id = $course->enrollments->random()->student_data->id;
        $random_event_id = $course->events->random()->id;
        // Act: when the teacher registers attendance for one student in one event
        $response = $this->json('POST', "/attendance", [
            'student' => $random_student_id,
            'event' => $random_event_id,
            'attendance' => 2,
        ]);


        // Assert: the attendance record appears for the correct student and event
        $this->assertTrue(User::find($random_student_id)->attendance->where('event_id', $random_event_id)->first()['attendance_type_id'] == 2);
        
        // another student does not have any attendance record
        $this->assertFalse(User::all()->except($random_student_id)->random()->attendance->where('event_id', $random_event_id)->first()['attendance_type_id'] == 2);

        // the student does not have attendance records in other events
        $this->assertFalse(User::find($random_student_id)->attendance->where('event_id', Event::all()->except($random_event_id)->random())->first()['attendance_type_id'] == 2);

    }

    public function test_that_a_teacher_can_register_attendance_only_for_their_own_classes()
    {

    }

    public function test_attendance_overview_per_course()
    {
        $this->seed('DatabaseSeeder');
        $period = factory(Period::class)->create();

        // Arrange a course with a teacher, some events, and students
        $teacher = factory(User::class)->create();
        $teacher->assignRole('teacher');
        $teacher->givePermissionTo('courses.view');
        $teacher->givePermissionTo('attendance.view');

        $course = factory(Course::class)->create([
            'teacher_id' => $teacher->id,
            'period_id' => $period->id,
            'exempt_attendance' => false,
        ]);
        
        $course->events()->create([
            'start' => Carbon::parse('10 days ago'),
            'end' => Carbon::parse('10 days ago'),
            'name' => 'test event 1',
            'course_id' => $course->id,
        ]);

        $course->events()->create([
            'start' => Carbon::parse('4 days ago'),
            'end' => Carbon::parse('4 days ago'),
            'name' => 'test event 2',
            'course_id' => $course->id,
        ]);

        $course->events()->create([
            'start' => Carbon::parse('2 days ago'),
            'end' => Carbon::parse('2 days ago'),
            'name' => 'test event 3',
            'course_id' => $course->id,
        ]);

        $course->events()->create([
            'start' => Carbon::parse('+2 days'),
            'end' => Carbon::parse('+2 days'),
            'name' => 'test event 4',
            'course_id' => $course->id,
        ]);


        for ($i=0; $i < 4; $i++) { 
            $student = factory(User::class)->create();
            $student->assignRole('student');
            $student->enroll($course);
        }

        \Auth::guard(backpack_guard_name())->login($teacher);
        // create relevant attendance records

        // act: when the admin gets the pending attendance
        $pending_attendance = (new Attendance)->get_pending_attendance($period);
        
        // we see the name of the past events without attendance
        $this->assertTrue($pending_attendance[1]['event'] == 'test event 1');
        $this->assertTrue($pending_attendance[2]['event'] == 'test event 2');
        $this->assertTrue($pending_attendance[3]['event'] == 'test event 3');

        // but we can't see the future event
        $this->assertTrue(count($pending_attendance) == 3);
    }


    public function test_absence_monitoring()
    {
        $this->seed('DatabaseSeeder');
        $period = factory(Period::class)->create();

        // Arrange a course with a teacher, some events, and students
        $teacher = factory(User::class)->create();
        $teacher->assignRole('teacher');
        $teacher->givePermissionTo('courses.view');
        $teacher->givePermissionTo('attendance.view');

        $course = factory(Course::class)->create([
            'teacher_id' => $teacher->id,
            'period_id' => $period->id,
            'exempt_attendance' => false,
        ]);
        
        $event1 = $course->events()->create([
            'start' => Carbon::parse('10 days ago'),
            'end' => Carbon::parse('10 days ago'),
            'name' => 'test event 1',
            'course_id' => $course->id,
        ]);

        $event2 = $course->events()->create([
            'start' => Carbon::parse('4 days ago'),
            'end' => Carbon::parse('4 days ago'),
            'name' => 'test event 2',
            'course_id' => $course->id,
        ]);


        for ($i=0; $i < 4; $i++) { 
            $student = factory(User::class)->create();
            $student->assignRole('student');
            $student->enroll($course);
        }

        \Auth::guard(backpack_guard_name())->login($teacher);

        // create relevant attendance records
        $student->attendance()->create(['event_id' => $event1->id, 'attendance_type_id' => 1]); // present
        $student->attendance()->create(['event_id' => $event2->id, 'attendance_type_id' => 4]); // absent

        // act: when the admin gets the pending attendance
        $absence_count = (new Attendance)->get_absence_count($period);
        
        // we see one absence for the student
        $this->assertTrue($absence_count[0]->user_id == $student->id);
        
        // but we can't see other records
        $this->assertTrue(count($absence_count) == 1);
    }
}
