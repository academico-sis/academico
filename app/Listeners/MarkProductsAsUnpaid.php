<?php

namespace App\Listeners;

use App\Models\Invoice;

class MarkProductsAsUnpaid
{
    public function handle($event)
    {
        /** @var Invoice $invoice */
        $invoice = $event->invoice;

        foreach ($invoice->enrollments as $enrollment) {
            $enrollment->product->markAsUnpaid();
        }

        foreach ($invoice->scheduledPayments as $scheduledPayment) {
            $scheduledPayment->product->update(['status' => 1]);
            $scheduledPayment->product->enrollment->markAsUnpaid();
        }
    }
}
