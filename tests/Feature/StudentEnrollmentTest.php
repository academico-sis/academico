<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Period;
use App\Models\Student;
use App\Models\Year;
use App\Services\Settings;
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
        app(Settings::class)->set(['current_period' => $period->id]);

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

    public function test_enroll_creates_a_new_enrollment_after_a_cancelled_one(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create();

        $cancelledId = $student->enroll($course);
        Enrollment::find($cancelledId)->update(['status_id' => 3]);

        $newId = $student->enroll($course);

        $this->assertNotEquals($cancelledId, $newId);
        $this->assertEquals(1, Enrollment::find($newId)->status_id);
    }

    public function test_enroll_refuses_a_full_course(): void
    {
        $course = Course::factory()->create(['spots' => 1]);
        Student::factory()->create()->enroll($course);

        $this->expectException(\OverflowException::class);

        Student::factory()->create()->enroll($course);
    }

    public function test_enroll_ignores_spots_when_the_course_has_no_limit(): void
    {
        $course = Course::factory()->create(['spots' => 0]);

        Student::factory()->count(3)->create()->each(fn (Student $student) => $student->enroll($course));

        $this->assertEquals(3, Enrollment::where('course_id', $course->id)->count());
    }

    public function test_enroll_lets_the_price_follow_the_course(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create(['price' => 120]);

        $enrollment = Enrollment::find($student->enroll($course));

        $this->assertNull($enrollment->getRawOriginal('total_price'));
        $this->assertEquals(120, $enrollment->total_price);
    }

    public function test_enrollment_refusal_reason(): void
    {
        $student = Student::factory()->create();
        $course = Course::factory()->create(['spots' => 1]);

        $this->assertNull($student->getEnrollmentRefusalReason($course));

        $student->enroll($course);

        $this->assertEquals(__('The student is already enrolled in this course.'), $student->getEnrollmentRefusalReason($course));
        $this->assertEquals(__('This course is full.'), Student::factory()->create()->getEnrollmentRefusalReason($course));
    }
}
