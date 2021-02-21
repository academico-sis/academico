<?php

namespace App\Listeners;

use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Mail;
use App\Mail\ExpiringPartnershipAlert;
use Carbon\Carbon;

class SendExpiringPartnershipsAlerts
{
    public function handle($event)
    {
        foreach ($event->partners as $partner) {
            Mail::to(config('settings.secretary_email'))->queue(new ExpiringPartnershipAlert($partner));

            $partner->last_alert_sent_at = Carbon::now()->format('Y-m-d');
            $partner->save();
        }
    }
}
