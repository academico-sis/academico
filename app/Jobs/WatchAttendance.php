<?php

namespace App\Jobs;

use App\Mail\AbsenceNotification;
use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Mail;

class WatchAttendance implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $attendance;
    public $tries = 5;

    public function __construct(Attendance $attendance)
    {
        $this->attendance = $attendance;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->attendance->attendance_type_id == 4) {
            // if so, send an email
            $student = $this->attendance->student;

            // CC to the teacher and the administration
            $otherRecipients = [];

            if ($this->attendance->event->teacher->email !== null) {
                array_push($otherRecipients, ['email' => $this->attendance->event->teacher->email]);
            }

            if (config('settings.manager_email') !== null) {
                array_push($otherRecipients, ['email' => config('settings.manager_email')]);
            }

            // also send to the student's contacts
            foreach ($this->attendance->student->contacts as $contact) {
                array_push($otherRecipients, ['email' => $contact->email]);
            }

            Mail::to($student->user->email)
            ->locale($student->locale)
            ->cc($otherRecipients)
            ->queue(new AbsenceNotification($this->attendance->event, $student->user));
        }
    }
}
