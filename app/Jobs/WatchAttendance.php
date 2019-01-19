<?php

namespace App\Jobs;

use App\Models\User;
use App\Models\Event;
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
        //$attendance = Attendance::where('user_id', $this->user->id)->where('event_id', $this->event->id)->first();
        
        if ($this->attendance->attendance_type_id == 4) {
            // if so, send an email
            $recipient = $this->attendance->student_data->email;

            Mail::to($recipient)
            ->queue(new AbsenceNotification($this->attendance->event));

            foreach ($this->attendance->additional_data as $contact)
            {
                Mail::to($contact->email)
                ->queue(new AbsenceNotification($this->attendance->event));
            }
        };
    }
}
