<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Course;
use App\Models\Student;
use App\Models\Attendance;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AttendanceTest extends TestCase
{
    use RefreshDatabase;
    
    public function setUp(): void
    {
        parent::setUp();
        
        $this->seed('DatabaseSeeder');
        
        // create a student user
        $student = factory(Student::class)->create();
        
        //create a course with events
        $course = factory(Course::class)->create();

    }

    /** @test */
    public function an_attendance_record_has_an_associated_student()
    {

    }
    
    /** @test */
    public function an_attendance_record_may_have_associated_contacts()
    {
        
        
    }
}
