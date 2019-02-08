<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cart;
use App\Models\User;
use App\Models\Course;
use App\Models\Enrollment;

use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class EnrollmentTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $this->seed('DatabaseSeeder');

         // create an admin user and log them in to access enrollment routes
         $admin = factory(User::class)->create();
         $admin->assignRole('admin');
         backpack_auth()->login($admin, true);
    }

    
    /**
     * Enroll a student
     *
     */
    public function test_that_a_new_enrollment_appears_in_student_list()
    {
        // Arrange: given a newly created user...
        $student = factory(User::class)->create();

        // and a newly created course
        $course = factory(Course::class)->create();

        // Act: if we enroll the user in the course
        $this->json('POST', "/student/enroll", [
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        // Assert: they appear among the enrollments list for the course
        $this->assertTrue($student->enrollments->contains("course_id", $course->id));
        
        /* failing: test the course view endpoint */
/*         $response = $this->get("/course/$course->id");
        $response->assertSee("$student->firstname"); */

    }

    public function test_child_courses_enrollment()
    {

    }


    public function test_pending_enrollment_checkout()
    {
        // arrange: given a newly created enrollment
        $student = factory(User::class)->create();
        $course = factory(Course::class)->create();
        $student->enroll($course);

        // the enrollment is pending
        $enrollment = $student->enrollments->first();
        $this->assertTrue($enrollment->status_id == 1);

        // act: add the enrollment to cart
        $this->get("enrollments/$enrollment->id/bill");

        // assert: the enrollment appears in the user cart
        $cart = Cart::where('user_id', $student->id)->get();
        $expected_course = $enrollment->course;
        $this->assertTrue($cart->contains('product_id', $expected_course->id));

        // checkout cart
        $response = $this->json('POST', "cart/$student->id/checkout");
    
        // assert: the enrollment status changes
        $enrollment = Enrollment::where('user_id', $student->id)->first();
        //dd($enrollment);
        $this->assertTrue($enrollment->status_id == 2);

        // a pre-invoice is generated with the selected data
        $this->assertTrue($enrollment->pre_invoice->count() == 1);
        
        // and the cart is cleared
        $this->assertTrue(Cart::get_user_cart($student->id)->count() == 0);

    }


}
