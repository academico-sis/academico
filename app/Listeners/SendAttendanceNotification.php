<?php

namespace App\Listeners;

use App\Events\AttendanceSavedEvent;
use App\Jobs\WatchAttendance;

class SendAttendanceNotification
{
    public function handle(AttendanceSavedEvent $event)
    {
        $attendance = $event->attendance;

        if (! config('academico.send_emails_for_absences') || $attendance->attendance_type_id !== 4) {
            return;
        }

        WatchAttendance::dispatch($attendance)->delay(now()->addMinutes(30));
    }
}
