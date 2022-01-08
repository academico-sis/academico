<?php

namespace App\Listeners;

use App\Models\Invoice;

class DeleteInvoiceDetails
{
    public function handle($event)
    {
        /** @var Invoice $invoice */
        $invoice = $event->invoice;

        $invoice->invoiceDetails()->delete();
    }
}
