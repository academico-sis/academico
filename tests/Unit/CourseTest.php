<?php

namespace Tests\Unit;

use App\Models\Course;
use App\Models\Event;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->weeksUntilCourseStart = 5;
        $this->weeksUntilCourseEnd = 15;
        $this->initialStartDate = now()->addWeeks($this->weeksUntilCourseStart);
        $this->initialEndDate = now()->addWeeks($this->weeksUntilCourseEnd);

        $this->eventDay = rand(0, 6);
        $this->expectedEvents = ($this->weeksUntilCourseEnd - $this->weeksUntilCourseStart);

        if (now()->format('w') == $this->eventDay) {
            $this->expectedEvents++;
        }
    }

    /**
     * if course dates have changed, sync all events.
     */
    public function testEventsCreationUponCourseDateChange()
    {
        // given a course and events
        $course = factory(Course::class)->create([
            'start_date' => $this->initialStartDate->format('Y-m-d'),
            'end_date' => $this->initialEndDate->format('Y-m-d'),
        ]);

        $course->times()->create([
            'course_id' => $course->id,
            'day' => $this->eventDay,
            'start' => '15:00',
            'end' => '17:00',
        ]);

        // we have the expected number of events
        $this->assertEquals($this->expectedEvents, $course->events->count());

        // extended in a random number of weeks
        $changeInWeeks = rand(2, 5);
        $extendedStartDate = now()->addWeeks($this->weeksUntilCourseStart - $changeInWeeks);
        $extendedEndDate = now()->addWeeks($this->weeksUntilCourseEnd + $changeInWeeks);

        // and no event should exist outside the course date range
        $this->assertEquals(0, Event::where('start', '<', $this->initialStartDate->startOfDay())->where('start', '>', $extendedStartDate->startOfDay())->count());
        $this->assertEquals(0, Event::where('end', '>', $this->initialEndDate->endOfDay())->where('end', '<', $extendedEndDate->endOfDay())->count());

        // if the course start date is extended
        $course->update([
            'start_date' => $extendedStartDate->format('Y-m-d'),
        ]);
        $course->refresh();

        // the events are created before
        $this->assertEquals($changeInWeeks, Event::where('start', '<', $this->initialStartDate->startOfDay())->where('start', '>', $extendedStartDate->startOfDay())->count());
        $this->assertEquals($this->expectedEvents + $changeInWeeks, $course->events->count());

        // if the course end date is extended
        $course->update([
            'end_date' => $extendedEndDate->format('Y-m-d'),
        ]);
        $course->refresh();

        // the events are created after
        $this->assertEquals($changeInWeeks, Event::where('end', '>', $this->initialEndDate->endOfDay())->where('end', '<', $extendedEndDate->endOfDay())->count());
        $this->assertEquals($this->expectedEvents + 2 * $changeInWeeks, $course->events->count());
    }

    public function testEventsDeletionUponCourseDateChange()
    {
        $course = factory(Course::class)->create([
            'start_date' => $this->initialStartDate->format('Y-m-d'),
            'end_date' => $this->initialEndDate->format('Y-m-d'),
        ]);
        $course->times()->create([
            'course_id' => $course->id,
            'day' => $this->eventDay,
            'start' => '15:00',
            'end' => '17:00',
        ]);

        // we have the expected number of events
        $this->assertEquals($this->expectedEvents, $course->events->count());

        // reduce in a random number of weeks
        $changeInWeeks = rand(1, 2);
        $reducedStartDate = now()->addWeeks($this->weeksUntilCourseStart + $changeInWeeks);
        $reducedEndDate = now()->addWeeks($this->weeksUntilCourseEnd - $changeInWeeks);

        // and there are events in the new intervals
        $this->assertEquals($changeInWeeks, Event::where('start', '<', $reducedStartDate->startOfDay())->where('start', '>', $this->initialStartDate->startOfDay())->count());
        $this->assertEquals($changeInWeeks, Event::where('end', '>', $reducedEndDate->endOfDay())->where('end', '<', $this->initialEndDate->endOfDay())->count());

        // if the course start date is changed
        $course->update([
            'start_date' => $reducedStartDate->format('Y-m-d'),
        ]);
        $course->refresh();

        // the events no events after update
        $this->assertEquals(0, Event::where('start', '<', $reducedStartDate->startOfDay())->where('start', '>', $this->initialStartDate->startOfDay())->count());
        $this->assertEquals($this->expectedEvents - $changeInWeeks, $course->events->count());

        // if the course end date is changed
        $course->update([
            'end_date' => $reducedEndDate->format('Y-m-d'),
        ]);
        $course->refresh();

        // the events no events after update
        $this->assertEquals(0, Event::where('end', '>', $reducedEndDate->endOfDay())->where('end', '<', $this->initialEndDate->endOfDay())->count());
        $this->assertEquals($this->expectedEvents - 2 * $changeInWeeks, $course->events->count());
    }

    public function testEventsAreChangedAfterCompletelyNewCourseDate()
    {
        // given a course and events
        $course = factory(Course::class)->create([
            'start_date' => $this->initialStartDate->format('Y-m-d'),
            'end_date' => $this->initialEndDate->format('Y-m-d'),
        ]);
        $course->times()->create([
            'course_id' => $course->id,
            'day' => $this->eventDay,
            'start' => '15:00',
            'end' => '17:00',
        ]);

        // we have the expected number of events
        $this->assertEquals($this->expectedEvents, $course->events->count());

        // change in a random number of weeks, greated than previous period
        $changeInWeeks = $this->expectedEvents * 2;
        $newStartDate = now()->addWeeks($this->weeksUntilCourseStart + $changeInWeeks);
        $newEndDate = now()->addWeeks($this->weeksUntilCourseEnd + $changeInWeeks);

        // and there are no events in the new interval
        $this->assertEquals(0, Event::where('start', '>', $newStartDate->startOfDay())->where('end', '>', $newEndDate->endOfDay())->count());

        // if the course start and end date is changed
        $course->update([
            'start_date' => $newStartDate->format('Y-m-d'),
            'end_date' => $newEndDate->format('Y-m-d'),
        ]);
        $course->refresh();

        // the events are created after
        $this->assertEquals($this->expectedEvents, Event::where('start', '>', $newStartDate->startOfDay())->where('end', '<', $newEndDate->endOfDay())->count());
        $this->assertEquals($this->expectedEvents, $course->events->count());
    }
}
