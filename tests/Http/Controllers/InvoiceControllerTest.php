<?php

namespace Tests\Http\Controllers;

use App\Http\Controllers\InvoiceController;
use App\Models\Enrollment;
use App\Models\Invoice;
use App\Models\Payment;
use App\Models\ScheduledPayment;
use App\Services\InternalInvoicingService;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('TestSeeder');
    }

    public function testEnrollmentIsMarkedAsPaidIfRegularInvoiceIsFullyPaid()
    {
        $enrollment = factory(Enrollment::class)->create();

        $invoice = new Invoice();
        $invoice->id = 1;
        $invoice->save();

        $invoice->invoiceDetails()->create([
            'invoice_id' => $invoice->id,
            'product_name' => 'enrollment',
            'product_code' => 'code',
            'product_id' => $enrollment->id,
            'product_type' => Enrollment::class,
            'price' => $enrollment->total_price,
            'final_price' => $enrollment->total_price,
            'quantity' => 1,
        ]);

        Payment::create([
            'responsable_id' => 1,
            'invoice_id' => $invoice->id,
            'payment_method' => null,
            'value' => $enrollment->total_price,
            'date' => Carbon::now(),
        ]);

        $this->assertFalse($enrollment->isPaid());

        (new InvoiceController(new InternalInvoicingService()))->ifTheInvoiceIsFullyPaidMarkItsProductsAsSuch($invoice);

        $this->assertTrue($enrollment->fresh()->isPaid());
    }

    public function testEnrollmentIsMarkedAsPaidIfScheduledPaymentsAreFullyPaid()
    {
        $enrollment = factory(Enrollment::class)->create();

        $invoice = new Invoice();
        $invoice->id = 1;
        $invoice->save();

        $invoice->invoiceDetails()->create([
            'invoice_id' => $invoice->id,
            'product_name' => 'enrollment',
            'product_code' => 'code',
            'product_id' => $enrollment->id,
            'product_type' => ScheduledPayment::class,
            'price' => $enrollment->total_price,
            'final_price' => $enrollment->total_price,
            'quantity' => 1,
        ]);

        $scheduledPayment = ScheduledPayment::create([
            'enrollment_id' => $enrollment->id,
            'value' => $enrollment->total_price,
            'status' => 1,
            'date' => Carbon::now(),
        ]);

        Payment::create([
            'responsable_id' => 1,
            'invoice_id' => $invoice->id,
            'payment_method' => null,
            'value' => $enrollment->total_price,
            'date' => Carbon::now(),
        ]);

        $this->assertFalse($enrollment->isPaid());
        $this->assertFalse($scheduledPayment->isPaid());

        (new InvoiceController(new InternalInvoicingService()))->ifTheInvoiceIsFullyPaidMarkItsProductsAsSuch($invoice);

        $this->assertTrue($enrollment->fresh()->isPaid());
        $this->assertTrue($scheduledPayment->fresh()->isPaid());
    }

    public function testEnrollmentIsNotMarkedAsPaidIfScheduledPaymentsAreNotFullyPaid()
    {
        $enrollment = factory(Enrollment::class)->create();

        $invoice = new Invoice();
        $invoice->id = 1;
        $invoice->save();

        $invoice->invoiceDetails()->create([
            'invoice_id' => $invoice->id,
            'product_name' => 'enrollment',
            'product_code' => 'code',
            'product_id' => $enrollment->id,
            'product_type' => ScheduledPayment::class,
            'price' => $enrollment->total_price,
            'final_price' => $enrollment->total_price,
            'quantity' => 1,
        ]);

        $scheduledPayment = ScheduledPayment::create([
            'enrollment_id' => $enrollment->id,
            'value' => $enrollment->total_price - 50,
            'status' => 1,
            'date' => Carbon::now(),
        ]);

        $remainingScheduledPayment = ScheduledPayment::create([
            'enrollment_id' => $enrollment->id,
            'value' => 50,
            'status' => 1,
            'date' => Carbon::now(),
        ]);

        Payment::create([
            'responsable_id' => 1,
            'invoice_id' => $invoice->id,
            'payment_method' => null,
            'value' => $enrollment->total_price,
            'date' => Carbon::now(),
        ]);

        $this->assertFalse($enrollment->isPaid());

        (new InvoiceController(new InternalInvoicingService()))->ifTheInvoiceIsFullyPaidMarkItsProductsAsSuch($invoice);

        $this->assertFalse($enrollment->fresh()->isPaid());
        $this->assertTrue($scheduledPayment->fresh()->isPaid());
        $this->assertFalse($remainingScheduledPayment->fresh()->isPaid());
    }

    public function testEnrollmentIsNotMarkedAsPaidIfRegularInvoiceIsNotFullyPaid()
    {
        $enrollment = factory(Enrollment::class)->create();

        $invoice = new Invoice();
        $invoice->id = 1;
        $invoice->save();

        $invoice->invoiceDetails()->create([
            'invoice_id' => $invoice->id,
            'product_name' => 'enrollment',
            'product_code' => 'code',
            'product_id' => $enrollment->id,
            'product_type' => Enrollment::class,
            'price' => 50,
            'final_price' => 50,
            'quantity' => 1,
        ]);

        Payment::create([
            'responsable_id' => 1,
            'invoice_id' => $invoice->id,
            'payment_method' => null,
            'value' => $enrollment->total_price,
            'date' => Carbon::now(),
        ]);

        $this->assertFalse($enrollment->isPaid());

        (new InvoiceController(new InternalInvoicingService()))->ifTheInvoiceIsFullyPaidMarkItsProductsAsSuch($invoice);

        $this->assertFalse($enrollment->fresh()->isPaid());
    }
}
