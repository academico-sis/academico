<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Models\Invoice;
use Illuminate\Console\Command;

class compare_invoice_relationsCommand extends Command
{
    protected $signature = 'academico:invoice-check';

    protected $description = 'Command description';

    public function handle()
    {
        $anomalies = 0;
        foreach (Invoice::all() as $invoice) {
            // get enrollments
            $enrollments = $invoice->enrollments->pluck('id')->toArray();
            $realEnrollments = $invoice->invoiceDetails()->where('product_type', Enrollment::class)->get()->pluck('product_id')->toArray();

            if (count(array_diff($enrollments, $realEnrollments)) > 0) {
                $anomalies++;
                echo "\ninvoice ".$invoice->id.' is linked to enrollments '.implode(' - ', $enrollments).'and really has '.implode(' - ', $realEnrollments);
            }
        }
        echo $anomalies.' anomalies detected';
    }
}
