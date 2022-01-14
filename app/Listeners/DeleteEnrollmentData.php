<?php

namespace App\Listeners;

use App\Events\EnrollmentDeleting;
use App\Models\InvoiceDetail;

class DeleteEnrollmentData
{
    public function handle(EnrollmentDeleting $event)
    {
        /** @var InvoiceDetail $invoiceDetail */
        foreach ($event->enrollment->invoiceDetails as $invoiceDetail) {
            $invoiceDetail->delete();
            if ($invoiceDetail->invoice->invoiceDetails->count() === 0) {
                $invoiceDetail->invoice->delete();
            }
        }
    }
}
