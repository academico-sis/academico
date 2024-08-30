<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoicingTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed('TestSeeder');
    }

    public function test_scheduled_payments_can_be_generated_for_enrollment(): void
    {
        $this->markTestIncomplete('Test unfinished');
    }

    public function test_invoice_can_be_generated_for_product(): void
    {
        $this->markTestIncomplete('Test unfinished');
    }

    public function test_invoice_can_be_generated_for_enrollment(): void
    {
        $this->markTestIncomplete('Test unfinished');
    }

    public function test_invoice_can_be_generated_for_scheduled_payment(): void
    {
        $this->markTestIncomplete('Test unfinished');
    }

    public function test_when_invoice_is_paid_enrollment_is_marked_as_paid(): void
    {
        $this->markTestIncomplete('Test unfinished');
    }

    public function when_invoice_is_paid_scheduled_payment_is_marked_as_paid()
    {
        $this->markTestIncomplete('Test unfinished');
    }

    public function test_invoice_balance_is_available(): void
    {
        $this->markTestIncomplete('Test unfinished');
    }

    public function test_invoice_total_price_is_available(): void
    {
        $this->markTestIncomplete('Test unfinished');
    }

    public function test_invoice_total_paid_price_is_available(): void
    {
        $this->markTestIncomplete('Test unfinished');
    }
}
