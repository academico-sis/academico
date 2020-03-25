<?php

namespace App\Mail;

use App\Models\Course;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ResultNotification extends Mailable
{
    use Queueable;
    use SerializesModels;

    public $course;
    public $student;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Course $course, $student)
    {
        $this->course = $course;
        $this->student = $student;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this
            ->subject(__('Result Notification'))
            ->view('emails.result_notification');
    }
}
