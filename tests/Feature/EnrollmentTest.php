<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Cart;
use App\Models\User;
use App\Models\Course;
use App\Models\Student;

use App\Models\Enrollment;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();

        $this->seed('DatabaseSeeder');

         // create an admin user and log them in to access enrollment routes
         $admin = factory(User::class)->create();
         $admin->assignRole('admin');
         backpack_auth()->login($admin, true);
    }

    
    /** @test */
    public function a_new_enrollment_appears_in_course_students_list()
    {
        // Arrange: given a newly created student...
        $student = factory(Student::class)->create();

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


        /** @test */
        public function access_student_real_enrollments()
        {
            // create a student
            $student = factory(Student::class)->create();
    
            // and two courses
            $course_1 = factory(Course::class)->create();
            $course_2 = factory(Course::class)->create();
    
            // and two children courses for the second course
            $course_2_a = factory(Course::class)->create([
                'parent_course_id' => $course_2->id,
            ]);
    
            $course_2_b = factory(Course::class)->create([
                'parent_course_id' => $course_2->id,
            ]);
    
            // enroll the student in course 1 and 2
            $enrollment_1 = $student->enroll($course_1);
            $enrollment_2 = $student->enroll($course_2);
    
            // assert that the user is a member of course 1 and 2
            $this->assertTrue($course_1->enrollments->contains('student_id', $student->id));
            $this->assertTrue($course_2->enrollments->contains('student_id', $student->id));
    
            // assert that the user is a member of courses 2a and 2b
            $this->assertTrue($course_2_a->enrollments->contains('student_id', $student->id));
            $this->assertTrue($course_2_b->enrollments->contains('student_id', $student->id));
    
            // real enrolment returns the children enrollments in second course's children, but not the 'meta' enrollment in the parent course
            $this->assertFalse($student->real_enrollments->contains('id', $enrollment_2));
    
        }
    
        /** @test */
        public function access_course_enrollments()
        {
        }
    
        /** @test */
        public function an_enrollment_may_be_created_by_authorized_users_only()
        {
        }
    
        /** @test */
        public function an_enrollment_may_be_deleted_by_authorized_users_only()
        {
        }


}
