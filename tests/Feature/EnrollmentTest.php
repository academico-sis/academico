<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class EnrollmentTest extends TestCase
{

    use RefreshDatabase;

    use DatabaseMigrations;

    
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_a_new_enrollment_appears_in_student_list()
    {
        $this->seed('DatabaseSeeder');

        // given a newly created user...
        $student = factory(User::class)->create();

        // and a newly created course
        $course = factory(Course::class)->create();

        // if we enroll the user in the course
        $student->enroll($course);

        // they appear among the enrollments list for the course
        $enrollments = $course->enrollments;
        $this->assertTrue($enrollments->contains($student->id));
    }


    public function test_pending_enrollment_checkout()
    {
        $this->seed('DatabaseSeeder');
        
        // arrange: given a newly created enrollment
        $student = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $student->enroll($course);

        // the enrollment is pending
        $enrollment = $student->enrollments->first();
        $this->assertTrue($enrollment->status_id == 1);

        // act: add the enrollment to cart
        $this->json('POST', "enrollments/$enrollment->id/bill", [
            'user_id' => $student->id,
            'firstname' => "Eva",
            'lastname' => "",
            'email' => "evita@example.com",
            'address' => "example 123 address",
            'idnumber' => "65656565FGFGFG",
        ]);

        // checkout cart
        $this->json('POST', "cart/$student->id/checkout", [

        ]);
        
        //dd($student->enrollments);
        // assert: the enrollment appears as paid with all relevant data
        $this->assertTrue($enrollment->status_id == 2);
    }
}
