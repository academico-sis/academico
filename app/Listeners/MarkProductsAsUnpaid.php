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
            if ($enrollment->product) {
                //$enrollment->product->markAsUnpaid();
            } else {
                \Sentry\captureMessage('Unable to delete invoice for enrollment #'.$enrollment->id);
            }
        }

        foreach ($invoice->scheduledPayments as $scheduledPayment) {
            $scheduledPayment->product->update(['status' => 1]);
            $scheduledPayment->product->enrollment->markAsUnpaid();
        }
    }
}
