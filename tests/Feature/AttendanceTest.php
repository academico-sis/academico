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

    public function setUp()
    {
        parent::setUp();

        $this->seed('DatabaseSeeder');

         // Arrange a course with a teacher, some events, and students
         $this->teacher = factory(User::class)->create();
         $this->teacher->assignRole('teacher');
         $this->teacher->givePermissionTo('attendance.edit');
         $this->teacher->givePermissionTo('attendance.view');
 
         $this->course = factory(Course::class)->create([
             'teacher_id' => $this->teacher->id,
         ]);
         
         $this->course->events()->create([
             'start' => Carbon::parse('10 days ago'),
             'end' => Carbon::parse('10 days ago'),
             'name' => 'test event',
         ]);
 
         $this->course->events()->create([
             'start' => Carbon::parse('4 days ago'),
             'end' => Carbon::parse('4 days ago'),
             'name' => 'test event',
         ]);
 
         $this->course->events()->create([
             'start' => Carbon::parse('2 days ago'),
             'end' => Carbon::parse('2 days ago'),
             'name' => 'test event',
         ]);
 
         $this->course->events()->create([
             'start' => Carbon::parse('+2 days'),
             'end' => Carbon::parse('+2 days'),
             'name' => 'test event',
         ]);
 
 
         for ($i=0; $i < 4; $i++) { 
             $this->student = factory(User::class)->create();
             $this->student->assignRole('student');
             $this->student->enroll($this->course);
         }
         
         \Auth::guard(backpack_guard_name())->login($this->teacher);

    }

    public function test_attendance_recording()
    {
        $random_student_id = $this->course->enrollments->random()->student->id;
        $random_event_id = $this->course->events->random()->id;
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
        // this feature is not yet implemented
    }

    public function test_attendance_overview_per_course()
    {
        // act: when the admin gets the pending attendance
        $pending_attendance = (new Attendance)->get_pending_attendance();
        
        // we see the name of the past events without attendance
        $this->assertTrue($pending_attendance[1]['event'] == 'test event 1');
        $this->assertTrue($pending_attendance[2]['event'] == 'test event 2');
        $this->assertTrue($pending_attendance[3]['event'] == 'test event 3');

        // but we can't see the future event
        $this->assertTrue(count($pending_attendance) == 3);
    }


    public function test_absence_monitoring()
    {
        // create relevant attendance records
        $this->student->attendance()->create(['event_id' => $event1->id, 'attendance_type_id' => 1]); // present
        $this->student->attendance()->create(['event_id' => $event2->id, 'attendance_type_id' => 4]); // absent

        // act: when the admin gets the pending attendance
        $absence_count = (new Attendance)->get_absence_count($period);
        
        // we see one absence for the student
        $this->assertTrue($absence_count[0]->user_id == $this->student->id);
        
        // but we can't see other records
        $this->assertTrue(count($absence_count) == 1);
    }
}
