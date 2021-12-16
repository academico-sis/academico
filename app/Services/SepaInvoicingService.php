<?php

namespace App\Services;

use App\Interfaces\InvoicingInterface;
use App\Models\Invoice;

class SepaInvoicingService implements InvoicingInterface
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
