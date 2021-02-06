<?php

namespace App\Listeners;

use App\Events\EnrollmentCreated;
use App\Events\EnrollmentUpdated;
use App\Events\UserCreated;
use App\Events\StudentDeleting;
use App\Events\StudentUpdated;
use App\Models\Enrollment;
use App\Traits\ApolearnApi;
use Carbon\Carbon;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class AddPastAttendance
{
    public function handle(EnrollmentCreated $event) : void
    {
        $enrollment = $event->enrollment;
        // when creating a new enrollment, also add past attendance
        $events = $enrollment->course->events->where('start', '<', (new Carbon())->toDateString());
        foreach ($events as $event) {
            $event->attendance()->create([
                'student_id' => $enrollment->student_id,
                'attendance_type_id' => 3,
            ]);
        }
    }
}
