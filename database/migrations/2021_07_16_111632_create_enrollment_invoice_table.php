<?php

use App\Models\Enrollment;
use App\Models\Invoice;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('enrollment_invoice', function (Blueprint $table) {
            $table->id();
            $table->unsignedInteger('enrollment_id')->references('id')->on('users')->onDelete('cascade');
            $table->unsignedInteger('invoice_id')->references('id')->on('invoices')->onDelete('cascade');
        });

        foreach (Enrollment::all() as $enrollment) {
            $invoices = Invoice::whereId($enrollment->invoice_id);

            if ($invoices->count() > 0) {
                foreach ($invoices->get() as $invoice) {
                    $enrollment->invoices()->attach($invoice);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('enrollment_invoice');
    }
};
