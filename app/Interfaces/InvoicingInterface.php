<?php

namespace App\Interfaces;

use App\Models\Invoice;

interface InvoicingInterface
{
    public function status() : bool;

    public function saveInvoice(Invoice $invoice): ?string;
}
