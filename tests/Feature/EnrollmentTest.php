<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cart;
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
     * Enroll a student
     *
     * @return void
     */
    public function test_that_a_new_enrollment_appears_in_student_list()
    {
        $this->seed('DatabaseSeeder');

        // create an admin user and log them in to access enrollment routes
        $admin = factory(User::class)->create();
        $admin->assignRole('admin');
        backpack_auth()->login($admin, true);

        // Arrange: given a newly created user...
        $student = factory(User::class)->create();

        // and a newly created course
        $course = factory(Course::class)->create();

        // Act: if we enroll the user in the course
        $this->json('POST', "/enrollments", [
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);

        // Assert: they appear among the enrollments list for the course
        $this->assertTrue($student->enrollments->contains("course_id", $course->id));
        
        /* todo: to test the course view endpoint we need a room, a level, a rythm */
        /* $response = $this->get("/course/$course->id");
        dd($response);
        $response->assertSee("$student->firstname"); */

        // todo also test a enrollment with children (make another test because we don't know if this feature will be kept in the future)
    }


    public function test_pending_enrollment_checkout()
    {
        $this->seed('DatabaseSeeder');
        
        // create an admin user and log them in to access protected routes
        $admin = factory(User::class)->create();
        $admin->assignRole('admin');
        backpack_auth()->login($admin, true);

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
        $expected_course = $enrollment->course_data;
        $this->assertTrue($cart->contains('product_id', $expected_course->id));

        // checkout cart
        $this->json('POST', "cart/$student->id/checkout", [

        ]);
    
        // assert: the enrollment status changes
        $this->assertTrue($enrollment->status_id == 2);
        
            // a pre-invoice is generated with the selected data
            
            // and the cart is cleared

    }

    public function test_cart_checkout()
    {
        // arrange: given an existing cart with an enrollment pending

        // act: when the enrollment is being paid
        
    }

}
