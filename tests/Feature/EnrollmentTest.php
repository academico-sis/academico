<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EnrollmentTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_a_new_enrollment_appears_in_student_list()
    {
        // given a user
        $student = \App\Student::find(1);

        // and a course
        $course = \App\Models\Course::find(1);
        
        // if we enroll the user in the course
        $student->enroll($course);

        // they appear on the student roaster
        $user = \App\Student::find(2);
        \Auth::guard(backpack_guard_name())->login($user);

        $response = $this->get("/courses/$course->id");
        
        // with their name
        $response->assertSee("$student->name");

        // todo and their age
    }
}
