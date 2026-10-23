<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Period;
use App\Models\Student;
use App\Models\Year;
use App\Services\StatService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class StatServiceTest extends TestCase
{
    use RefreshDatabase;

    private Year $year;

    private Period $period;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('enrollment_status_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Pending'])],
            ['id' => 2, 'name' => json_encode(['fr' => 'Paid'])],
            ['id' => 3, 'name' => json_encode(['fr' => 'Cancelled'])],
        ]);

        $this->year = Year::factory()->create();
        $this->period = Period::factory()->create(['year_id' => $this->year->id]);
    }

    public function test_courses_count_for_period(): void
    {
        Course::factory()->count(3)->create(['period_id' => $this->period->id, 'partner_id' => null]);

        $stats = new StatService(external: false, reference: $this->period);

        $this->assertEquals(3, $stats->coursesCount());
    }

    public function test_courses_count_excludes_external_when_internal(): void
    {
        Course::factory()->create(['period_id' => $this->period->id, 'partner_id' => null]);
        Course::factory()->create(['period_id' => $this->period->id, 'partner_id' => 1]);

        $stats = new StatService(external: false, reference: $this->period);

        $this->assertEquals(1, $stats->coursesCount());
    }

    public function test_enrollments_count_sums_pending_and_paid(): void
    {
        $course = Course::factory()->create(['period_id' => $this->period->id, 'partner_id' => null]);

        // Pending
        Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $course->id,
            'status_id' => 1,
        ]);

        // Paid
        Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $course->id,
            'status_id' => 2,
        ]);

        // Cancelled (should not count)
        Enrollment::create([
            'student_id' => Student::factory()->create()->id,
            'course_id' => $course->id,
            'status_id' => 3,
        ]);

        $stats = new StatService(external: false, reference: $this->period);

        $this->assertEquals(2, $stats->enrollmentsCount());
    }

    public function test_students_count_distinct(): void
    {
        $course1 = Course::factory()->create(['period_id' => $this->period->id, 'partner_id' => null]);
        $course2 = Course::factory()->create(['period_id' => $this->period->id, 'partner_id' => null]);

        $student = Student::factory()->create();

        // Same student enrolled in two courses
        Enrollment::create(['student_id' => $student->id, 'course_id' => $course1->id, 'status_id' => 1]);
        Enrollment::create(['student_id' => $student->id, 'course_id' => $course2->id, 'status_id' => 1]);

        $stats = new StatService(external: false, reference: $this->period);

        $this->assertEquals(1, $stats->studentsCount());
    }

    public function test_students_count_by_gender(): void
    {
        $course = Course::factory()->create(['period_id' => $this->period->id, 'partner_id' => null]);

        $female = Student::factory()->create(['gender_id' => 1]);
        $male = Student::factory()->create(['gender_id' => 2]);

        Enrollment::create(['student_id' => $female->id, 'course_id' => $course->id, 'status_id' => 1]);
        Enrollment::create(['student_id' => $male->id, 'course_id' => $course->id, 'status_id' => 1]);

        $stats = new StatService(external: false, reference: $this->period);

        $this->assertEquals(1, $stats->studentsCount(1)); // female
        $this->assertEquals(1, $stats->studentsCount(2)); // male
    }

    public function test_taught_hours_excludes_children(): void
    {
        $parentCourse = Course::factory()->create([
            'period_id' => $this->period->id,
            'partner_id' => null,
            'volume' => 40,
            'remote_volume' => 10,
        ]);
        Course::factory()->create([
            'period_id' => $this->period->id,
            'partner_id' => null,
            'parent_course_id' => $parentCourse->id,
            'volume' => 20,
            'remote_volume' => 5,
        ]);

        $stats = new StatService(external: false, reference: $this->period);

        // Only parent course counted: 40 + 10 = 50
        $this->assertEquals(50, $stats->taughtHoursCount());
    }

    public function test_sold_hours_multiplies_volume_by_enrollments(): void
    {
        $course = Course::factory()->create([
            'period_id' => $this->period->id,
            'partner_id' => null,
            'volume' => 30,
            'remote_volume' => 10,
        ]);

        // 2 enrollments
        Enrollment::create(['student_id' => Student::factory()->create()->id, 'course_id' => $course->id, 'status_id' => 1]);
        Enrollment::create(['student_id' => Student::factory()->create()->id, 'course_id' => $course->id, 'status_id' => 2]);

        $stats = new StatService(external: false, reference: $this->period);

        // (30 + 10) * 2 = 80
        $this->assertEquals(80, $stats->soldHoursCount());
    }

    public function test_hours_count_courses_without_remote_volume(): void
    {
        $course = Course::factory()->create([
            'period_id' => $this->period->id,
            'partner_id' => null,
            'volume' => 40,
            'remote_volume' => null,
        ]);
        Course::factory()->create([
            'period_id' => $this->period->id,
            'partner_id' => null,
            'volume' => null,
            'remote_volume' => 10,
        ]);

        Enrollment::create(['student_id' => Student::factory()->create()->id, 'course_id' => $course->id, 'status_id' => 1]);

        $stats = new StatService(external: false, reference: $this->period);

        $this->assertEquals(50, $stats->taughtHoursCount());
        $this->assertEquals(40, $stats->soldHoursCount());
    }

    public function test_paid_and_pending_enrollment_counts(): void
    {
        $course = Course::factory()->create(['period_id' => $this->period->id, 'partner_id' => null]);

        Enrollment::create(['student_id' => Student::factory()->create()->id, 'course_id' => $course->id, 'status_id' => 1]);
        Enrollment::create(['student_id' => Student::factory()->create()->id, 'course_id' => $course->id, 'status_id' => 1]);
        Enrollment::create(['student_id' => Student::factory()->create()->id, 'course_id' => $course->id, 'status_id' => 2]);

        $stats = new StatService(external: false, reference: $this->period);

        $this->assertEquals(2, $stats->pendingEnrollmentsCount());
        $this->assertEquals(1, $stats->paidEnrollmentsCount());
    }
}
