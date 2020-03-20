<?php

namespace App\Mail;

use App\Models\Enrollment;
use App\Models\Event;
use App\Models\Student;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AbsenceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $student;
    public $enrollment;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Event $event, User $student)
    {
        $this->event = $event;
        $this->student = $student;
        $nstudent = Student::where('user_id', $student->id)->first();
        $this->enrollment = Enrollment::where('student_id', $nstudent->id)->where('course_id', $event->course_id)->first();
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(__('Absence Notification'))
            ->view('emails.absence_notification');
    }
}
