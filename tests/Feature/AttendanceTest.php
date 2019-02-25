<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Course;
use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->seed('DatabaseSeeder');
        
        // Arrange a course with some events, and students
        $this->course = factory(Course::class)->create(); // todo allow coursetime creation from the course factory
        
        $this->course->teacher->user->givePermissionTo('attendance.edit');
        $this->course->teacher->user->givePermissionTo('attendance.view');

        // add a coursetime, which will create events
        $this->course->times()->create([
            'day' => 3,
            'start' => '15:45:00',
            'end' => '17:45:00'
            ]);
            
            for ($i=0; $i < 4; $i++) { 
                $student = factory(Student::class)->create();
                $student->enroll($this->course);
            }
        }
        
        /** @test */
        public function authorized_users_can_take_attendance()
        {
            \Auth::guard(backpack_guard_name())->login($this->course->teacher->user);

            $student = $this->course->enrollments()->first()->student;

            $attributes = [
                'student_id' => $student->id,
                'event_id' => $this->course->events->first()->id,
                'attendance_type_id' => 1,
            ];

            // when the teacher posts a new attendance record
            $this->json('POST', route('storeAttendance'), $attributes);

            // it is persisted to the DB
            $this->assertDatabaseHas('attendances', $attributes);
        }
        

        /** @test */
        public function unauthorized_users_cannot_take_attendance()
        {

            // guests can not use the post route

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
            $user->givePermissionTo('attendance.edit');
            $user->givePermissionTo('attendance.view');

            \Auth::guard(backpack_guard_name())->login($user);

            $this->json('POST', route('storeAttendance'), $attributes)->assertStatus(403);
            $this->assertDatabaseMissing('attendances', $attributes);

        }
        
        public function test_attendance_overview_per_course()
        {
        }
        
        
        public function test_absence_monitoring()
        {
        }
    }
    