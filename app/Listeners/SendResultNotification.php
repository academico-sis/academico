<?php

namespace App\Listeners;

use App\Events\ResultSavedEvent;
use App\Mail\ResultNotification;
use Illuminate\Support\Facades\Mail;

class SendResultNotification
{
    public function handle(ResultSavedEvent $event)
    {
        if (! config('academico.send_emails_for_results')) {
            return;
        }

        $result = $event->result;

        Mail::to($result->enrollment->student->user->email)
            ->locale($result->enrollment->student->user->locale)
            ->queue(new ResultNotification($result->enrollment->course, $result->enrollment->student->user));
    }
}
