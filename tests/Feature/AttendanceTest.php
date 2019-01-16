<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Event;
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
        // arrange a course with 3 events and 3 students
        // create relevant attendance records

        // act: when a user visits the course attendance page
        $response = $this->get('/course');
        dd($response);
        // assert: they see the expected attendance records
    }
}
