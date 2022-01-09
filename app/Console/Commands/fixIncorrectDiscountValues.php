<?php

namespace App\Console\Commands;

use App\Models\Discount;
use App\Models\Enrollment;
use App\Models\Invoice;
use Illuminate\Console\Command;

class fixIncorrectDiscountValues extends Command
{
    protected $signature = 'academico:fix-discount-values';

    protected $description = 'Command description';

    public function handle()
    {
        $anomalies = 0;
        foreach (Invoice::all() as $invoice) {
            if ($invoice->invoiceDetails->where('product_type', Discount::class)->count() > 0) {
                $enrollmentValue = $invoice->invoiceDetails->where('product_type', Enrollment::class)->sum('price');
                foreach ($invoice->invoiceDetails->where('product_type', Discount::class) as $discount) {
                    $discountValue = $discount->price / 100;
                    $discount->update(['price' => ($discountValue * $enrollmentValue)]);
                    $anomalies++;
                    echo "\ninvoice ".$invoice->id . "fixed";
                }

            }
        }
        echo "\n".$anomalies.' anomalies fixed';
    }
}
