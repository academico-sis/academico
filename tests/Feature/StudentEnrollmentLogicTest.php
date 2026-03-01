<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentEnrollmentLogicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('enrollment_status_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Pending'])],
            ['id' => 2, 'name' => json_encode(['fr' => 'Paid'])],
        ]);
    }

    public function test_enroll_creates_enrollment(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create();

        $student->enroll($course);

        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_enroll_returns_enrollment_id(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create();

        $enrollmentId = $student->enroll($course);

        $this->assertIsInt($enrollmentId);
        $this->assertNotNull(Enrollment::find($enrollmentId));
    }

    public function test_enroll_deduplicates_same_course(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create();

        $id1 = $student->enroll($course);
        $id2 = $student->enroll($course);

        $this->assertEquals($id1, $id2);
        $this->assertEquals(1, Enrollment::where('student_id', $student->id)->where('course_id', $course->id)->count());
    }

    public function test_enroll_auto_enrolls_in_child_courses(): void
    {
        $student = Student::factory()->create();
        $parentCourse = Course::factory()->create();
        $childCourse1 = Course::factory()->create(['parent_course_id' => $parentCourse->id]);
        $childCourse2 = Course::factory()->create(['parent_course_id' => $parentCourse->id]);

        $student->enroll($parentCourse);

        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'course_id' => $childCourse1->id,
        ]);
        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'course_id' => $childCourse2->id,
        ]);
    }

    public function test_enroll_sets_child_enrollment_parent_id(): void
    {
        $student = Student::factory()->create();
        $parentCourse = Course::factory()->create();
        $childCourse = Course::factory()->create(['parent_course_id' => $parentCourse->id]);

        $parentEnrollmentId = $student->enroll($parentCourse);

        $childEnrollment = Enrollment::where('student_id', $student->id)
            ->where('course_id', $childCourse->id)
            ->first();

        $this->assertEquals($parentEnrollmentId, $childEnrollment->parent_id);
    }

    public function test_enroll_clears_lead_type(): void
    {
        $student = Student::factory()->create(['lead_type_id' => 5]);
        $course = Course::factory()->create();

        $student->enroll($course);

        $this->assertNull($student->fresh()->lead_type_id);
    }

    public function test_student_age_accessor(): void
    {
        $student = Student::factory()->create([
            'birthdate' => now()->subYears(25)->format('Y-m-d'),
        ]);

        $this->assertStringContainsString('25', $student->student_age);
        $this->assertStringContainsString(__('years old'), $student->student_age);
    }

    public function test_student_age_empty_when_no_birthdate(): void
    {
        $student = Student::factory()->create(['birthdate' => null]);

        $this->assertEquals('', $student->student_age);
    }

    public function test_formatted_gender_accessor(): void
    {
        $female = Student::factory()->create(['gender_id' => 1]);
        $male = Student::factory()->create(['gender_id' => 2]);
        $unset = Student::factory()->create(['gender_id' => null]);

        $this->assertEquals('F', $female->formatted_gender);
        $this->assertEquals('M', $male->formatted_gender);
        $this->assertEquals('', $unset->formatted_gender);
    }
}
