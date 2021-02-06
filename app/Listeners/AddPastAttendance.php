<?php

namespace App\Listeners;

use App\Events\EnrollmentCreated;
use App\Events\EnrollmentUpdated;
use App\Events\StudentCreated;
use App\Events\StudentDeleting;
use App\Events\StudentUpdated;
use App\Traits\ApolearnApi;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddPastAttendance
{
    public function handle(EnrollmentCreated $event) : void
    {
        // when creating a new enrollment, also add past attendance
        $events = $event->enrollment->course->events->where('start', '<', (new Carbon())->toDateString());

        foreach ($events as $event) {
            $event->attendance()->create([
                'student_id' => $event->enrollment->student_id,
                'attendance_type_id' => 3,
            ]);
        }
    }
}
