<?php

namespace App\Listeners;

use App\Events\LeaveCreated;
use App\Events\LeaveUpdated;

class UpdateTeacherEvents
{
    public function handle(LeaveCreated|LeaveUpdated $event)
    {
        $teacher = $event->leave->teacher;
        foreach ($teacher->events()->whereDate('start', '>=', $event->leave->date)->whereDate('end', '<=', $event->leave->date)->get() as $event) {
            $event->teacher_id = null;
            $event->save();
        }
    }
}
