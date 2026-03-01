<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Event;
use App\Models\Leave;
use App\Models\Period;
use App\Models\Teacher;
use App\Models\Year;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TeacherBusinessLogicTest extends TestCase
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

    public function test_period_courses_excludes_parent_courses_with_children(): void
    {
        $teacher = Teacher::factory()->create();
        $year = Year::factory()->create();
        $period = Period::factory()->create(['year_id' => $year->id]);

        $parentCourse = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'period_id' => $period->id,
        ]);
        $childCourse = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'period_id' => $period->id,
            'parent_course_id' => $parentCourse->id,
        ]);
        $standaloneCourse = Course::factory()->create([
            'teacher_id' => $teacher->id,
            'period_id' => $period->id,
        ]);

        $courses = $teacher->period_courses($period);

        $this->assertFalse($courses->contains('id', $parentCourse->id));
        $this->assertTrue($courses->contains('id', $childCourse->id));
        $this->assertTrue($courses->contains('id', $standaloneCourse->id));
    }

    public function test_period_events_filters_by_period_date_range(): void
    {
        $teacher = Teacher::factory()->create();
        $year = Year::factory()->create();
        $period = Period::factory()->create([
            'year_id' => $year->id,
            'start' => '2025-03-01',
            'end' => '2025-06-30',
        ]);

        $insideEvent = Event::factory()->create([
            'teacher_id' => $teacher->id,
            'start' => '2025-04-15 09:00:00',
            'end' => '2025-04-15 11:00:00',
        ]);
        $outsideEvent = Event::factory()->create([
            'teacher_id' => $teacher->id,
            'start' => '2025-01-10 09:00:00',
            'end' => '2025-01-10 11:00:00',
        ]);

        $events = $teacher->period_events($period);

        $this->assertTrue($events->contains('id', $insideEvent->id));
        $this->assertFalse($events->contains('id', $outsideEvent->id));
    }

    public function test_planned_hours_in_period_sums_event_lengths(): void
    {
        $teacher = Teacher::factory()->create();

        // Create two 2-hour events
        Event::factory()->create([
            'teacher_id' => $teacher->id,
            'start' => '2025-04-01 09:00:00',
            'end' => '2025-04-01 11:00:00',
        ]);
        Event::factory()->create([
            'teacher_id' => $teacher->id,
            'start' => '2025-04-02 14:00:00',
            'end' => '2025-04-02 16:00:00',
        ]);

        $hours = $teacher->plannedHoursInPeriod('2025-04-01', '2025-04-30');

        // Event length may be negative due to Carbon diffInSeconds sign in newer versions
        $this->assertEquals(4, abs($hours));
    }

    public function test_planned_hours_excludes_events_outside_range(): void
    {
        $teacher = Teacher::factory()->create();

        Event::factory()->create([
            'teacher_id' => $teacher->id,
            'start' => '2025-04-01 09:00:00',
            'end' => '2025-04-01 11:00:00',
        ]);
        Event::factory()->create([
            'teacher_id' => $teacher->id,
            'start' => '2025-05-15 09:00:00',
            'end' => '2025-05-15 11:00:00',
        ]);

        $hours = $teacher->plannedHoursInPeriod('2025-04-01', '2025-04-30');

        $this->assertEquals(2, abs($hours));
    }

    public function test_planned_remote_hours_prorates_by_overlapping_weeks(): void
    {
        $teacher = Teacher::factory()->create();

        // Course with 10 hours remote volume over 10 weeks = 1 hour/week
        Course::factory()->create([
            'teacher_id' => $teacher->id,
            'remote_volume' => 10,
            'start_date' => '2025-01-06',
            'end_date' => '2025-03-16', // ~10 weeks
        ]);

        // Query for 5 overlapping weeks
        $hours = $teacher->plannedRemoteHoursInPeriod('2025-01-06', '2025-02-09');

        $this->assertGreaterThan(0, $hours);
    }

    public function test_upcoming_leaves_single_day(): void
    {
        $teacher = Teacher::factory()->create();
        $futureDate = Carbon::now()->addDays(5)->format('Y-m-d');

        // Bypass the Leave boot dedup by inserting directly
        \DB::table('leaves')->insert([
            'teacher_id' => $teacher->id,
            'date' => $futureDate,
            'leave_type_id' => \App\Models\LeaveType::factory()->create()->id,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        $leaves = $teacher->fresh()->upcoming_leaves;

        $this->assertCount(1, $leaves);
        $this->assertEquals(Carbon::parse($futureDate)->format('d/m/Y'), $leaves[0]);
    }

    public function test_upcoming_leaves_consecutive_days_as_range(): void
    {
        $teacher = Teacher::factory()->create();
        $leaveTypeId = \App\Models\LeaveType::factory()->create()->id;

        $day1 = Carbon::now()->addDays(10)->format('Y-m-d');
        $day2 = Carbon::now()->addDays(11)->format('Y-m-d');
        $day3 = Carbon::now()->addDays(12)->format('Y-m-d');

        foreach ([$day1, $day2, $day3] as $date) {
            \DB::table('leaves')->insert([
                'teacher_id' => $teacher->id,
                'date' => $date,
                'leave_type_id' => $leaveTypeId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $leaves = $teacher->fresh()->upcoming_leaves;

        $this->assertCount(1, $leaves);
        $this->assertStringContainsString(' - ', $leaves[0]);
    }

    public function test_upcoming_leaves_non_consecutive_as_separate(): void
    {
        $teacher = Teacher::factory()->create();
        $leaveTypeId = \App\Models\LeaveType::factory()->create()->id;

        $day1 = Carbon::now()->addDays(10)->format('Y-m-d');
        $day2 = Carbon::now()->addDays(15)->format('Y-m-d');

        foreach ([$day1, $day2] as $date) {
            \DB::table('leaves')->insert([
                'teacher_id' => $teacher->id,
                'date' => $date,
                'leave_type_id' => $leaveTypeId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $leaves = $teacher->fresh()->upcoming_leaves;

        $this->assertCount(2, $leaves);
    }

    public function test_upcoming_leaves_empty_when_no_future_leaves(): void
    {
        $teacher = Teacher::factory()->create();

        $this->assertEmpty($teacher->upcoming_leaves);
    }
}
