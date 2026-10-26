<?php

namespace App\Console\Commands;

use App\Mail\PendingAttendanceReminder;
use App\Models\Period;
use App\Models\Teacher;
use App\Traits\RunsAtInstitutionTime;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class SendAttendanceReminders extends Command
{
    use RunsAtInstitutionTime;

    protected $signature = 'academico:attendance-reminders
        {--at= : Only run at this time (HH:MM, on a quarter of an hour) in the institution timezone}
        {--day= : Only run on this day of the month in the institution timezone}';

    protected $description = 'Email the teachers whose classes of the current period still have no attendance a day after they took place';

    public function handle(): int
    {
        if (! $this->isDue()) {
            return Command::SUCCESS;
        }

        if (! config('academico.send_attendance_reminders')) {
            $this->info('Attendance reminders are turned off.');

            return Command::SUCCESS;
        }

        if (! Period::exists()) {
            return Command::SUCCESS;
        }

        $period = Period::get_default_period();

        foreach (Teacher::all() as $teacher) {
            $events = $teacher->events_with_pending_attendance($period)
                ->where('start', '<', Carbon::parse('24 hours ago')->toDateTimeString());

            if ($events->isNotEmpty()) {
                Mail::to($teacher->email)
                    ->locale($teacher->user?->locale ?? config('academico.locale'))
                    ->queue(new PendingAttendanceReminder($teacher, $events));
            }
        }

        return Command::SUCCESS;
    }
}
