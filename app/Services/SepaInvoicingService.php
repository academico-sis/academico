<?php

namespace App\Services;

use App\Models\Invoice;

class SepaInvoicingService implements \App\Interfaces\InvoicingInterface
{
    public function status(): bool
    {
        return false;
    }

    public function saveInvoice(Invoice $invoice): ?string
    {
        return 'ok';
    }
}
