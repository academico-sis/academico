<?php

namespace Tests\Feature;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\Course;
use App\Models\Student;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;
    
    public function setUp()
    {
        parent::setUp();
        
        $this->seed('DatabaseSeeder'); // needed?
        
        // Arrange a course with some events, and students
        $this->course = factory(Course::class)->create(); // todo allow coursetime creation from the course factory
        
        // add a coursetime, which will create events
        $this->course->times()->create([
            'day' => 3,
            'start' => '15:45:00',
            'end' => '17:45:00'
            ]);
            
            for ($i=0; $i < 4; $i++) { 
                factory(Student::class)->create();
                //$this->student->enroll($this->course);
            }
            
        }
        
        /** @test */
        public function take_attendance()
        {
            
        }
        
        public function test_that_a_teacher_can_register_attendance_only_for_their_own_classes()
        {
            // this feature is not yet implemented
        }
        
        public function test_attendance_overview_per_course()
        {
        }
        
        
        public function test_absence_monitoring()
        {
        }
    }
    