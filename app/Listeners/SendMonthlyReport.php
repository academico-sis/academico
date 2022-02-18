<?php

namespace App\Listeners;

use App\Events\MonthlyReportEvent;
use App\Mail\MonthlyReport;
use Illuminate\Support\Facades\Mail;

class SendMonthlyReport
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @return void
     */
    public function handle(MonthlyReportEvent $event)
    {
        $recipients = [];

        if (config('settings.reports_email') !== null) {
            $recipients[] = ['email' => explode(',', config('settings.reports_email'))];
        }

        Mail::to($recipients)->queue(new MonthlyReport());
    }
}
