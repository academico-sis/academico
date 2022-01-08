<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\ScheduledPayment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class fixIncorrectProductTypesCommand extends Command
{
    protected $signature = 'academico:fix-product-types';

    protected $description = 'Command description';

    public function handle()
    {
        $fixedAnomaliesCount = 0;
        $unsolvedMysteries = 0;

        foreach (Invoice::all() as $invoice) {
            // get enrollments
            $products = $invoice->invoiceDetails()->where('product_type', Enrollment::class)->get();

            foreach ($products as $product) {
                $validEnrollments = DB::table('enrollment_invoice')->where('invoice_id', $invoice->id)->get();
                if ($validEnrollments->doesntContain('enrollment_id', $product->product_id)) {
                    echo "\nmaybe product $product->product_id is not an enrollment";
                    if ($validEnrollments->contains('scheduled_payment_id', $product->product_id)) {
                        $product->update(['product_type' => ScheduledPayment::class]);
                        $fixedAnomaliesCount++;
                    } else {
                        $unsolvedMysteries++;
                    }
                }
                else {
                    echo "\nproduct $product->product_id seems to be a valid enrollment";
                }
            }
}
        echo "\n".$fixedAnomaliesCount.' inconsistencies fixed and ' . $unsolvedMysteries . ' remaining.';
    }
}
