<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * if course dates have changed, sync all events.
     */
    public function testEventsCreationUponCourseDateChange()
    {
        // given a course and events
        $initialStartDate = '2020-03-16'; // todo randomize
        $initialEndDate = '2020-03-28'; // todo randomize

        $course = factory(Course::class)->create([
            'start_date' => $initialStartDate,
            'end_date' => $initialEndDate,
        ]);

        $course->times()->create([
            'course_id' => $course->id,
            'day' => 2, // todo randomize
            'start' => '15:00',
            'end' => '17:00',
        ]);

        // the course should have 2 events
        $this->assertEquals(2, $course->events->count());

        // and no event should exist outside the course date range
        $extendedStartDate = '2020-03-09';
        $extendedEndDate = '2020-04-04';

        $this->assertEquals(0, Event::where('start', '<', $initialStartDate)->where('start', '>', $extendedStartDate)->count());
        $this->assertEquals(0, Event::where('end', '>', $initialEndDate)->where('end', '<', $extendedEndDate)->count());

        // if the course dates are extended
        $course->update([
            'start_date' => $extendedStartDate,
        ]);

        $course1 = Course::find($course->id);
        // the events are created before
        $this->assertEquals(2, $course->events->count());
        $this->assertEquals(1, Event::where('start', '<', $initialStartDate)->where('start', '>', $extendedStartDate)->count());

        $course->update([
            'end_date' => $extendedEndDate,
        ]);

        $course2 = Course::find($course->id);
        $this->assertEquals(4, $course2->events->count());
        $this->assertEquals(1, Event::where('end', '>', $initialEndDate)->where('end', '<', $extendedEndDate)->count());
    }

    public function testEventsDeletionUponCourseDateChange()
    {
        // given a course and events
        // if the course dates are reduced the events are deleted before
        // if the course dates are reduced the events are deleted after
        $this->assertTrue(true);
    }
}
