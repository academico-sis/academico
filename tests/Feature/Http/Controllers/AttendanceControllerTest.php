<?php

namespace Tests\Feature\Http\Controllers;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Student;
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
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        $user = User::first();
        //$user->assignRole('admin');

        \Auth::guard(backpack_guard_name())->login($user);
        
        // given a course with some past classes
        $course = factory(Course::class)->create();
        $course->times()->create(['day' => 1, 'start' => '09:00:00', 'end' => '17:00:00']);

        // and a student enrolled in the course
        $student = factory(Student::class)->create();
        $student->enroll($course);

        // when the admin browses to the attendance dashboard
        $response = $this->get('/attendance');

        // they should see the same of the course and the name of the student
        $response->assertSee($course->name);
        $response->assertSee($student->firstname);
    }
}
