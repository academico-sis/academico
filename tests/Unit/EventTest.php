<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Course;
use App\Models\Period;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class EventTest extends TestCase
{
    use DatabaseMigrations;
   
    public function test_that_events_are_created_with_new_coursetime()
    {
        // given a course
        $course = factory(Course::class)->create([
            'start_date' => "2019-01-01", // Tuesday
            'end_date' => "2019-01-05"
        ]);

        // when a coursetime is added
        $course->times()->create([
            'course_id' => $course->id,
            'day' => 4, // Thursday
            'start' => "15:00",
            'end' => "17:00",
            ]);
            
        // an event with the date of the coursetime should exist     
        $this->assertEquals("2019-01-03 15:00:00", $course->events->first()->start);
    }

    public function test_that_events_are_deleted_with_coursetime()
    {
        // given a course
        $course = factory(Course::class)->create([
            'start_date' => "2019-01-01", // Tuesday
            'end_date' => "2019-01-06"
        ]);

        // with 2 weekly events
        $course->times()->create([
            'course_id' => $course->id,
            'day' => 4,
            'start' => "15:00",
            'end' => "17:00",
        ]);

        $course->times()->create([
            'course_id' => $course->id,
            'day' => 6,
            'start' => "09:00",
            'end' => "10:30",
        ]);

        // when a coursetime is deleted

        dd($course->events);
        $coursetime = $course->times->where('day', 4);
        $coursetime->delete();

        // related events are also deleted

        //$this->assertFalse($course->events->contains('start'), ))
    }
}
