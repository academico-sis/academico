<?php

namespace App\Traits;

use App\Mail\PendingAttendanceReminder;
use App\Models\Period;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

trait HandlesAttendance
{
    /** Send email reminders to all teachers who have classes with incomplete attendance records */
    public function remindPendingAttendance()
    {
        $period = Period::get_default_period();
        foreach (Teacher::all() as $teacher) {
            $events = $teacher->events_with_pending_attendance($period)
                ->where('start', '<', (Carbon::parse('24 hours ago'))->toDateTimeString());

            if ($events->count() > 0) {
                Mail::to($teacher->email)
                    ->locale('fr')
                    ->queue(new PendingAttendanceReminder($teacher, $events));
            }
        }
    }
}
