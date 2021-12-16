<?php

namespace App\Services;

use App\Interfaces\InvoicingInterface;
use App\Models\Invoice;

class InternalInvoicingService implements InvoicingInterface
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
