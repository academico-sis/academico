<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AdminReminders extends Mailable
{
    use Queueable, SerializesModels;

    public $changeNextPeriod;
    public $changeCurrentPeriod;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($changeNextPeriod, $changeCurrentPeriod)
    {
        $this->changeNextPeriod = $changeNextPeriod;
        $this->changeCurrentPeriod = $changeCurrentPeriod;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject(__('Default Periods on Academico'))->view('emails.admin_reminders');
    }
}
