<?php

namespace Tests\Unit;

use App\Models\Campus;
use App\Models\Course;
use App\Models\Level;
use App\Models\Period;
use App\Models\Rhythm;
use App\Models\Room;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EventTest extends TestCase
{
    use RefreshDatabase;

    public function test_that_events_are_created_with_new_coursetime()
    {
        $rhythm = factory(Rhythm::class)->create();
        $level = factory(Level::class)->create();
        $period = factory(Period::class)->create();
        $campus = factory(Campus::class)->create();
        $room = factory(Room::class)->create([
            'campus_id' => $campus->id,
        ]);

        // given a course
        $course = factory(Course::class)->create([
            'rhythm_id' => $rhythm->id,
            'room_id' => $room->id,
            'level_id' => $level->id,
            'campus_id' => $campus->id,
            'period_id' => $period->id,
            'start_date' => '2019-01-01', // Tuesday
            'end_date' => '2019-01-05',
        ]);

        // when a coursetime is added
        $course->times()->create([
            'course_id' => $course->id,
            'day' => 4, // Thursday
            'start' => '15:00',
            'end' => '17:00',
        ]);

        // an event with the date of the coursetime should exist
        $this->assertEquals('2019-01-03 15:00:00', $course->events->first()->start);
    }

    public function test_that_events_are_deleted_with_coursetime()
    {
        $rhythm = factory(Rhythm::class)->create();
        $level = factory(Level::class)->create();
        $period = factory(Period::class)->create();
        $campus = factory(Campus::class)->create();
        $room = factory(Room::class)->create([
            'campus_id' => $campus->id,
        ]);

        // given a course
        $course = factory(Course::class)->create([
            'rhythm_id' => $rhythm->id,
            'level_id' => $level->id,
            'room_id' => $room->id,
            'campus_id' => $campus->id,
            'period_id' => $period->id,
            'start_date' => '2019-01-01', // Tuesday = day 2
            'end_date' => '2019-01-06', // sunday = day 0
        ]);

        // with 2 weekly events
        $course->times()->create([
            'course_id' => $course->id,
            'day' => 4,
            'start' => '15:00',
            'end' => '17:00',
        ]);

        $course->times()->create([
            'course_id' => $course->id,
            'day' => 6,
            'start' => '09:00',
            'end' => '10:30',
        ]);

        // when a coursetime is deleted

        $coursetime = $course->times->where('day', 4)->first();
        $coursetime->delete();

        // related events are also deleted
        $this->assertFalse($course->events->contains('start', '2019-01-03 15:00:00'));

        // but events related to other coursetimes are still present
        $this->assertTrue($course->events->contains('start', '2019-01-05 09:00:00'));
    }
}
