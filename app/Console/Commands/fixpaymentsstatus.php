<?php

namespace App\Console\Commands;

use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\ScheduledPayment;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class fixpaymentsstatus extends Command
{
    protected $signature = 'academico:fix-payment-status';

    protected $description = 'Command description';

    public function handle()
    {
        foreach (ScheduledPayment::whereNull('status')->get() as $payment) {
            $payment->update(['status' => $payment->computed_status]);
        }
    }
}
