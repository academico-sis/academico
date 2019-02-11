<?php

namespace App\Mail;

use App\Models\Event;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Contracts\Queue\ShouldQueue;

class AbsenceNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $event;
    public $student;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(Event $event, User $student)
    {
        $this->event = $event;
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
            ->subject(__('Absence Notification'))
            ->view('emails.absence_notification');
    }
}
