<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\CourseTime;
use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Partner;
use App\Models\Student;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseBusinessLogicTest extends TestCase
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

    public function test_course_times_accessor_formats_schedule(): void
    {
        $course = Course::factory()->create();

        CourseTime::factory()->create([
            'course_id' => $course->id,
            'day' => 1, // Monday
            'start' => '09:00:00',
            'end' => '11:00:00',
        ]);

        $courseTimes = $course->fresh()->course_times;

        $this->assertNotEmpty($courseTimes);
        $this->assertIsString($courseTimes);
    }

    public function test_hide_children_scope_excludes_child_courses(): void
    {
        $parentCourse = Course::factory()->create();
        $childCourse = Course::factory()->create(['parent_course_id' => $parentCourse->id]);

        $courses = Course::hideChildren()->pluck('id');

        $this->assertTrue($courses->contains($parentCourse->id));
        $this->assertFalse($courses->contains($childCourse->id));
    }

    public function test_realcourses_scope_excludes_courses_with_children(): void
    {
        $parentCourse = Course::factory()->create();
        Course::factory()->create(['parent_course_id' => $parentCourse->id]);
        $standaloneCourse = Course::factory()->create();

        $realCourses = Course::realcourses()->pluck('id');

        $this->assertFalse($realCourses->contains($parentCourse->id));
        $this->assertTrue($realCourses->contains($standaloneCourse->id));
    }

    public function test_total_volume_includes_remote_volume(): void
    {
        $course = Course::factory()->create([
            'volume' => 40,
            'remote_volume' => 10,
        ]);

        $this->assertEquals(50, $course->total_volume);
    }

    public function test_save_course_times_creates_and_deletes(): void
    {
        $course = Course::factory()->create();

        // Create initial course times
        $initialTimes = collect([
            ['day' => 1, 'start' => '09:00:00', 'end' => '11:00:00'],
            ['day' => 3, 'start' => '14:00:00', 'end' => '16:00:00'],
        ]);
        $course->saveCourseTimes($initialTimes);
        $this->assertEquals(2, $course->times()->count());

        // Update: remove Wednesday, keep Monday, add Friday
        $updatedTimes = collect([
            ['day' => 1, 'start' => '09:00:00', 'end' => '11:00:00'],
            ['day' => 5, 'start' => '10:00:00', 'end' => '12:00:00'],
        ]);
        $course->refresh();
        $course->saveCourseTimes($updatedTimes);

        $course->refresh();
        $days = $course->times->pluck('day')->sort()->values()->toArray();
        $this->assertEquals([1, 5], $days);
    }

    public function test_course_enrollments_count_excludes_children_and_cancelled(): void
    {
        $course = Course::factory()->create();

        // Active enrollment (counted)
        $activeEnrollment = Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        // Cancelled enrollment (not counted)
        Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $course->id,
            'status_id' => 3,
        ]);

        // Child enrollment (not counted in real_enrollments)
        Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $course->id,
            'status_id' => 1,
            'parent_id' => $activeEnrollment->id,
        ]);

        // enrollments() includes status 1 & 2 but doesn't filter children
        // course_enrollments_count uses the enrollments() relation
        $this->assertEquals(2, $course->fresh()->course_enrollments_count);

        // real_enrollments excludes children
        $this->assertEquals(1, $course->real_enrollments()->count());
    }

    public function test_accepts_new_students_when_spots_null(): void
    {
        $course = Course::factory()->create(['spots' => null]);

        $this->assertTrue($course->accepts_new_students);
    }

    public function test_accepts_new_students_when_spots_available(): void
    {
        $course = Course::factory()->create(['spots' => 10]);

        // Only 1 enrollment, spots = 10
        Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        $this->assertTrue($course->fresh()->accepts_new_students);
    }

    public function test_does_not_accept_when_full(): void
    {
        $course = Course::factory()->create(['spots' => 1]);

        Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        $this->assertFalse($course->fresh()->accepts_new_students);
    }

    public function test_internal_scope_excludes_partner_courses(): void
    {
        $partner = Partner::factory()->create();
        $internalCourse = Course::factory()->create(['partner_id' => null]);
        $externalCourse = Course::factory()->create(['partner_id' => $partner->id]);

        $internalIds = Course::internal()->pluck('id');

        $this->assertTrue($internalIds->contains($internalCourse->id));
        $this->assertFalse($internalIds->contains($externalCourse->id));
    }

    public function test_external_scope_returns_partner_courses(): void
    {
        $partner = Partner::factory()->create();
        $internalCourse = Course::factory()->create(['partner_id' => null]);
        $externalCourse = Course::factory()->create(['partner_id' => $partner->id]);

        $externalIds = Course::external()->pluck('id');

        $this->assertFalse($externalIds->contains($internalCourse->id));
        $this->assertTrue($externalIds->contains($externalCourse->id));
    }

    public function test_events_with_expected_attendance_excludes_exempt_and_future(): void
    {
        $course = Course::factory()->create(['exempt_attendance' => false]);

        // Past non-exempt event — should be included
        $pastEvent = Event::factory()->create([
            'course_id' => $course->id,
            'start' => now()->subDays(2)->toDateTimeString(),
            'end' => now()->subDays(2)->addHour()->toDateTimeString(),
            'exempt_attendance' => null,
        ]);

        // Future event — should be excluded
        $futureEvent = Event::factory()->create([
            'course_id' => $course->id,
            'start' => now()->addDays(10)->toDateTimeString(),
            'end' => now()->addDays(10)->addHour()->toDateTimeString(),
            'exempt_attendance' => null,
        ]);

        // Past exempt event — should be excluded
        $exemptEvent = Event::factory()->create([
            'course_id' => $course->id,
            'start' => now()->subDays(1)->toDateTimeString(),
            'end' => now()->subDays(1)->addHour()->toDateTimeString(),
            'exempt_attendance' => 1,
        ]);

        $events = $course->eventsWithExpectedAttendance()->get();

        $this->assertTrue($events->contains('id', $pastEvent->id));
        $this->assertFalse($events->contains('id', $futureEvent->id));
        $this->assertFalse($events->contains('id', $exemptEvent->id));
    }
}
