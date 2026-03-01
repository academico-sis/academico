<?php

namespace Tests\Feature;

use App\Models\Config;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Period;
use App\Models\Student;
use App\Models\Year;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StudentEnrollmentTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('enrollment_status_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Pending'])],
            ['id' => 2, 'name' => json_encode(['fr' => 'Paid'])],
            ['id' => 3, 'name' => json_encode(['fr' => 'Cancelled'])],
        ]);
    }

    public function test_enroll_creates_enrollment(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create();

        $enrollmentId = $student->enroll($course);

        $this->assertDatabaseHas('enrollments', [
            'id' => $enrollmentId,
            'student_id' => $student->id,
            'course_id' => $course->id,
        ]);
    }

    public function test_enroll_creates_children_for_child_courses(): void
    {
        $student = Student::factory()->create();
        $parentCourse = Course::factory()->create();
        $childCourse = Course::factory()->create(['parent_course_id' => $parentCourse->id]);

        $enrollmentId = $student->enroll($parentCourse);

        $this->assertDatabaseHas('enrollments', [
            'student_id' => $student->id,
            'course_id' => $childCourse->id,
            'parent_id' => $enrollmentId,
        ]);
    }

    public function test_enroll_avoids_duplicate_enrollment(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create();

        $firstId = $student->enroll($course);
        $secondId = $student->enroll($course);

        $this->assertEquals($firstId, $secondId);
        $this->assertEquals(1, Enrollment::where('student_id', $student->id)->where('course_id', $course->id)->count());
    }

    public function test_enroll_clears_lead_type(): void
    {
        $student = Student::factory()->create(['lead_type_id' => 1]);
        $course = Course::factory()->create();

        $student->enroll($course);

        $this->assertNull($student->fresh()->lead_type_id);
    }

    public function test_enrolled_scope_returns_students_in_default_period(): void
    {
        $year = Year::factory()->create();
        $period = Period::factory()->create(['year_id' => $year->id]);
        Config::where('name', 'current_period')->update(['value' => $period->id]);

        $course = Course::factory()->create(['period_id' => $period->id]);

        $enrolledStudent = Student::factory()->create();
        Enrollment::create([
            'student_id' => $enrolledStudent->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        $notEnrolledStudent = Student::factory()->create();

        $enrolled = Student::enrolled()->pluck('id');

        $this->assertTrue($enrolled->contains($enrolledStudent->id));
        $this->assertFalse($enrolled->contains($notEnrolledStudent->id));
    }
}
