<?php

namespace App\Listeners;

use App\Models\Invoice;

class DeleteInvoiceDetails
{
    public function handle($event): void
    {
        /** @var Invoice $invoice */
        $invoice = $event->invoice;

        $invoice->invoiceDetails()->delete();
    }
}
