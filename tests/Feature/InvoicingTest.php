<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('TestSeeder');
    }

    public function test_scheduled_payments_can_be_generated_for_enrollment()
    {

    }

    public function test_invoice_can_be_generated_for_product()
    {

    }

    public function test_invoice_can_be_generated_for_enrollment()
    {

    }

    public function test_invoice_can_be_generated_for_scheduled_payment()
    {

    }

    public function test_when_invoice_is_paid_enrollment_is_marked_as_paid()
    {

    }

    public function when_invoice_is_paid_scheduled_payment_is_marked_as_paid()
    {

    }

    public function test_invoice_balance_is_available()
    {

    }

    public function test_invoice_total_price_is_available()
    {

    }

    public function test_invoice_total_paid_price_is_available()
    {

    }
}
