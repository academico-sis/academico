<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseFeatureTest extends TestCase
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

    public function test_course_accepts_new_students_when_spots_available(): void
    {
        $course = Course::factory()->create(['spots' => 5]);

        $this->assertTrue($course->accepts_new_students);
    }

    public function test_course_rejects_new_students_when_full(): void
    {
        $course = Course::factory()->create(['spots' => 1]);

        Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        $this->assertFalse($course->fresh()->accepts_new_students);
    }

    public function test_course_accepts_unlimited_students_when_spots_is_null(): void
    {
        $course = Course::factory()->create(['spots' => null]);

        for ($i = 0; $i < 20; $i++) {
            Enrollment::create([
                'student_id' => Student::factory()->create()->id,
                'course_id' => $course->id,
                'status_id' => 1,
            ]);
        }

        $this->assertTrue($course->fresh()->accepts_new_students);
    }

    public function test_course_enrollment_count_reflects_active_enrollments(): void
    {
        $course = Course::factory()->create();

        Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $course->id,
            'status_id' => 2,
        ]);

        // Cancelled enrollment should not be counted
        Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $course->id,
            'status_id' => 3,
        ]);

        $this->assertEquals(2, $course->fresh()->course_enrollments_count);
    }

    public function test_sessions_added_by_hand_are_kept_when_dates_change(): void
    {
        $course = Course::factory()->create([
            'start_date' => now()->subDays(5)->format('Y-m-d'),
            'end_date' => now()->addDays(5)->format('Y-m-d'),
        ]);

        // not generated from a time slot of the course
        $sessionAddedByHand = Event::factory()->create([
            'course_id' => $course->id,
            'start' => now()->toDateTimeString(),
            'end' => now()->addHour()->toDateTimeString(),
        ]);

        $course->start_date = now()->subDays(2)->format('Y-m-d');
        $course->end_date = now()->addDays(2)->format('Y-m-d');
        $course->save();

        $this->assertModelExists($sessionAddedByHand);
    }

    public function test_real_enrollments_excludes_child_enrollments(): void
    {
        $course = Course::factory()->create();
        $student = Student::factory()->create();

        $parentEnrollment = Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        Enrollment::create([
            'student_id' => $student->id,
            'course_id' => $course->id,
            'status_id' => 1,
            'parent_id' => $parentEnrollment->id,
        ]);

        $this->assertEquals(1, $course->real_enrollments()->count());
    }

    public function test_course_children_relationship(): void
    {
        $parentCourse = Course::factory()->create();
        $childCourse1 = Course::factory()->create(['parent_course_id' => $parentCourse->id]);
        $childCourse2 = Course::factory()->create(['parent_course_id' => $parentCourse->id]);

        $this->assertEquals(2, $parentCourse->children()->count());
        $this->assertTrue($parentCourse->children->contains($childCourse1));
        $this->assertTrue($parentCourse->children->contains($childCourse2));
    }

    public function test_internal_and_external_course_scopes(): void
    {
        $internalCourse = Course::factory()->create(['partner_id' => null]);
        $externalCourse = Course::factory()->create(['partner_id' => 1]);

        $this->assertTrue(Course::internal()->get()->contains($internalCourse));
        $this->assertFalse(Course::internal()->get()->contains($externalCourse));

        $this->assertTrue(Course::external()->get()->contains($externalCourse));
        $this->assertFalse(Course::external()->get()->contains($internalCourse));
    }
}
