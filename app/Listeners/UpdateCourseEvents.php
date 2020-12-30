<?php

namespace App\Listeners;

use App\Events\CourseUpdated;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class UpdateCourseEvents
{
    public function handle(CourseUpdated $event)
    {
        $course = $event->course;

        // if course dates have changed, sync all events
        if (($course->events->count() > 0) && ($course->isDirty('start_date') || $course->isDirty('end_date'))) {
            Log::info('cleaning the events after course date change');

            // create events before first existing event and after course start
            $firstEvent = $course->events()->reorder('start')->first();
            $firstEventDate = Carbon::parse($firstEvent->start)->startOfDay();
            $lastEvent = $course->events()->reorder('start', 'desc')->first();
            $lastEventDate = Carbon::parse($lastEvent->start)->endOfDay();

            $courseStartDate = Carbon::parse($course->start_date)->startOfDay();
            $courseEndDate = Carbon::parse($course->end_date)->startOfDay();

            // delete events before course start date
            Event::where('course_id', $course->id)->where('start', '<', $courseStartDate->startOfDay())->delete();
            // delete events after course end date
            Event::where('course_id', $course->id)->where('end', '>', $courseEndDate->endOfDay())->delete();

            // for each day before the first event
            while ($courseStartDate < $courseEndDate) {
                // Event already exists
                if (($firstEventDate <= $courseStartDate) && ($courseStartDate <= $lastEventDate)) {
                    $courseStartDate->addDay();
                    continue;
                }

                // if there is a coursetime for today, create the event
                if ($course->times->contains('day', $courseStartDate->format('w'))) {
                    Event::create([
                        'course_id' => $course->id,
                        'teacher_id' => $course->teacher_id,
                        'room_id' => $course->room_id,
                        'start' => $courseStartDate->setTimeFromTimeString($course->times->where('day', $courseStartDate->format('w'))->first()->start)->toDateTimeString(),
                        'end' => $courseStartDate->setTimeFromTimeString($course->times->where('day', $courseStartDate->format('w'))->first()->end)->toDateTimeString(),
                        'name' => $course->name,
                        'course_time_id' => $course->times->where('day', $courseStartDate->format('w'))->first()->id,
                        'exempt_attendance' => $course->exempt_attendance,
                    ]);
                }
                $courseStartDate->addDay();
            }
        }

        // update course events with new room, teacher and name
        Event::where('course_id', $course->id)->update(['room_id' => $course->room_id]);
        Event::where('course_id', $course->id)->update(['teacher_id' => $course->teacher_id]);
        Event::where('course_id', $course->id)->update(['name' => $course->name]);
    }
}
