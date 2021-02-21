<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class ExternalCourseReport extends Mailable
{
    use Queueable, SerializesModels;

    public $period_start;
    public $period_end;
    public $data;

    public function __construct($period_start, $period_end, $data)
    {
        $this->period_start = $period_start;
        $this->period_end = $period_end;
        $this->data = $data;
    }

    public function build()
    {
        return $this->subject('Facturation ' . $this->data['partner_name'])->view('emails.external_courses_report');
    }
}
