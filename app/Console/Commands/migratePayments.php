<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Models\Invoice;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class migratePayments extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'migrate:payments';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    public function handle() : void
    {
        foreach (Enrollment::all() as $enrollment) {

            $payments = DB::table('payments')->where('enrollment_id', $enrollment->id)->get();

            if ($payments->count() > 0) {

                foreach ($payments as $payment) {
                    // generate an Invoice
                    $invoice = Invoice::create([
                        'invoice_type_id' => 1,
                        'receipt_number' => $payment->receipt_id,
                        'total_price' => $payment->value / 100,
                        'date' => $payment->created_at,
                        'created_at' => $payment->created_at,
                    ]);

                    $invoice->setNumber();

                    $enrollment->invoices()->attach($invoice);

                    DB::table('payments')->where('id', $payment->id)->update(['invoice_id' => $invoice->id]);
                }


            }
        }

    }
}
