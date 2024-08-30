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

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public Course $course, public $student)
    {
    }

    /**
     * Build the message.
     */
    public function build(): static
    {
        return $this
            ->subject(__('Result Notification'))
            ->view('emails.result_notification');
    }
}
