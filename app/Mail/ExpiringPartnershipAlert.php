<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExpiringPartnershipAlert extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct(public $partner)
    {
    }

    public function build()
    {
        return $this->subject('Expiration prochaine d\'un convenio')->view('emails.expiring_partnership_alert');
    }
}
