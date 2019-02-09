<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Models\Teacher;

class PendingAttendanceReminder extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $teacher;
    public $events;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Teacher $teacher, $events)
    {
        $this->teacher = $teacher;
        $this->events = $events;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->view('emails.attendance_reminder');
    }
}
