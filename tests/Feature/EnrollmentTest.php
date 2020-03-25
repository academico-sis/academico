<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed('TestSeeder');

        // create an admin user and log them in to access enrollment routes
        $admin = factory(User::class)->create();
        $admin->assignRole('admin');
        backpack_auth()->login($admin, true);
    }

    /** @test */
    public function authorized_users_may_enroll_students()
    {
        $admin = factory(User::class)->create();
        $admin->assignRole('admin');
        backpack_auth()->login($admin, true);

        // Arrange: given a newly created student...
        $student = factory(Student::class)->create();

        // and a newly created course
        $course = factory(Course::class)->create();

        // Act: if we enroll the user in the course
        $response = $this->json('POST', '/student/enroll', [
            'student_id' => $student->id,
            'course_id'  => $course->id,
        ]);

        // Assert: they appear among the enrollments list for the course
        $this->assertTrue($student->enrollments->contains('course_id', $course->id));
    }

    /** @test
     * A teacher should not be allowed to perform enrollments unless the course is theirs
     */
    public function unauthorized_teachers_may_not_enroll_students()
    {
        $teacher = factory(Teacher::class)->create();
        backpack_auth()->login($teacher->user, true);

        // Arrange: given a newly created student...
        $student = factory(Student::class)->create();

        // and a newly created course
        $course = factory(Course::class)->create();

        // Act: if we enroll the user in the course
        $response = $this->json('POST', '/student/enroll', [
            'student_id' => $student->id,
            'course_id'  => $course->id,
        ]);

        // Assert: the operation is unauthorized and the enrollment is not created
        $response->assertStatus(403);
        $this->assertFalse($student->enrollments->contains('course_id', $course->id));
    }

    /** @test
     * A teacher should not be allowed to perform enrollments unless the course is theirs
     */
    public function teachers_may_enroll_students_in_their_course()
    {
        $teacher = factory(Teacher::class)->create();
        backpack_auth()->login($teacher->user, true);

        // Arrange: given a newly created student...
        $student = factory(Student::class)->create();

        // and a newly created course
        $course = factory(Course::class)->create([
            'teacher_id' => $teacher->id,
        ]);

        // Act: if we enroll the user in the course
        $response = $this->json('POST', '/student/enroll', [
            'student_id' => $student->id,
            'course_id'  => $course->id,
        ]);

        // Assert: the operation is authorized and the enrollment is created
        $response->assertStatus(200);
        $this->assertTrue($student->enrollments->contains('course_id', $course->id));
    }

    /** @test
     * if an enrollment is created in a parent course; enrollments are automatically created in children courses as well
     * The "real_enrollments" method on the student class allows to return student enrollments excluding "meta" enrollments in parent courses
     */
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
    public function an_enrollment_may_be_deleted_by_authorized_users_only()
    {
    }

    /** @test */
    public function an_enrollment_may_be_changed_by_authorized_users()
    {
        // given 2 courses
        $courseA = factory(Course::class)->create();
        $courseB = factory(Course::class)->create();

        // given an enrollment in course A
        $enrollment = factory(Enrollment::class)->create([
            'course_id'  => $courseA->id,
            'student_id' => factory(Student::class)->create(),
        ]);

        // if we change the enrollment
        $enrollment->changeCourse($courseB);

        // it now belongs to courseB
        $this->assertEquals($courseB->id, $enrollment->course_id);
    }
}
