<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Models\Invoice;
use Illuminate\Console\Command;

class compare_invoice_pricesCommand extends Command
{
    protected $signature = 'academico:check-invoice-price';

    protected $description = 'Command description';

    public function handle()
    {
        $anomalies = 0;
        foreach (Invoice::all() as $invoice) {
            if ($invoice->total_price !== $invoice->totalPrice()) {
                $anomalies++;
                echo "\ninvoice ".$invoice->id. ' should be ' . $invoice->total_price . ' vs ' . $invoice->totalpricebyproducts() .' real ';
            }
        }
        echo "\n".$anomalies.' anomalies detected';
    }
}
