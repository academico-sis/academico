<?php


namespace App\Services;


use App\Models\Invoice;

class InternalInvoicingService implements \App\Interfaces\InvoicingInterface
{

    public function status(): bool
    {
        return true;
    }

    public function saveInvoice(Invoice $invoice): ?string
    {
        return null;
    }
}
