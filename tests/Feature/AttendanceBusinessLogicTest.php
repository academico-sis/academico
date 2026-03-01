<?php

namespace Tests\Feature;

use App\Models\Attendance;
use App\Models\Course;
use App\Models\Event;
use App\Models\Period;
use App\Models\Student;
use App\Models\Year;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AttendanceBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        \DB::table('attendance_types')->insert([
            ['id' => 1, 'name' => json_encode(['fr' => 'Present'])],
            ['id' => 2, 'name' => json_encode(['fr' => 'Late'])],
            ['id' => 3, 'name' => json_encode(['fr' => 'Absent - Justified'])],
            ['id' => 4, 'name' => json_encode(['fr' => 'Absent - Unjustified'])],
        ]);
    }

    public function test_groups_absences_by_student(): void
    {
        $year = Year::factory()->create();
        $period = Period::factory()->create(['year_id' => $year->id]);
        $course = Course::factory()->create(['period_id' => $period->id]);

        $student1 = Student::factory()->create();
        $student2 = Student::factory()->create();

        $event1 = Event::factory()->create(['course_id' => $course->id, 'start' => now(), 'end' => now()->addHour()]);
        $event2 = Event::factory()->create(['course_id' => $course->id, 'start' => now()->addDay(), 'end' => now()->addDay()->addHour()]);

        Attendance::create(['student_id' => $student1->id, 'event_id' => $event1->id, 'attendance_type_id' => 3]);
        Attendance::create(['student_id' => $student1->id, 'event_id' => $event2->id, 'attendance_type_id' => 4]);
        Attendance::create(['student_id' => $student2->id, 'event_id' => $event1->id, 'attendance_type_id' => 4]);

        $result = (new Attendance)->get_absence_count_per_student($period);

        $this->assertCount(2, $result);
        $this->assertEquals(2, $result[$student1->id]->count());
        $this->assertEquals(1, $result[$student2->id]->count());
    }

    public function test_excludes_present_and_late_types(): void
    {
        $year = Year::factory()->create();
        $period = Period::factory()->create(['year_id' => $year->id]);
        $course = Course::factory()->create(['period_id' => $period->id]);
        $student = Student::factory()->create();

        $event1 = Event::factory()->create(['course_id' => $course->id, 'start' => now(), 'end' => now()->addHour()]);
        $event2 = Event::factory()->create(['course_id' => $course->id, 'start' => now()->addDay(), 'end' => now()->addDay()->addHour()]);

        Attendance::create(['student_id' => $student->id, 'event_id' => $event1->id, 'attendance_type_id' => 1]); // Present
        Attendance::create(['student_id' => $student->id, 'event_id' => $event2->id, 'attendance_type_id' => 2]); // Late

        $result = (new Attendance)->get_absence_count_per_student($period);

        $this->assertCount(0, $result);
    }

    public function test_returns_empty_for_no_absences(): void
    {
        $year = Year::factory()->create();
        $period = Period::factory()->create(['year_id' => $year->id]);

        $result = (new Attendance)->get_absence_count_per_student($period);

        $this->assertCount(0, $result);
    }
}
