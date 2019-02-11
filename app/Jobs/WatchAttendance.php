<?php

namespace App\Jobs;

use App\Models\Attendance;
use Illuminate\Bus\Queueable;
use App\Mail\AbsenceNotification;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

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

            Mail::to($student->user->email)
            ->locale($student->locale)
            ->queue(new AbsenceNotification($this->attendance->event, $student->user));

            foreach ($this->attendance->student->contacts as $contact)
            {
                Mail::to($contact->email)
                ->locale($contact->locale)
                ->queue(new AbsenceNotification($this->attendance->event, $student->user));
            }
        };
    }
}
