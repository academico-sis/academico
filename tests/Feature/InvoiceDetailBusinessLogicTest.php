<?php

namespace Tests\Feature;

use App\Models\InvoiceDetail;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class InvoiceDetailBusinessLogicTest extends TestCase
{
    use RefreshDatabase;

    public function test_final_price_divides_stored_value_by_100(): void
    {
        $detail = InvoiceDetail::factory()->create();

        // Set raw final_price in DB as cents
        \DB::table('invoice_details')->where('id', $detail->id)->update(['final_price' => 5000]);

        $detail = $detail->fresh();
        $this->assertEquals(50, $detail->final_price);
    }

    public function test_final_price_falls_back_to_price_when_null(): void
    {
        $detail = InvoiceDetail::factory()->create(['price' => 75]);

        // Ensure final_price is null in DB
        \DB::table('invoice_details')->where('id', $detail->id)->update(['final_price' => null]);

        $detail = $detail->fresh();
        $this->assertEquals($detail->price, $detail->final_price);
    }

    public function test_total_price_returns_zero_when_no_raw_value(): void
    {
        $detail = InvoiceDetail::factory()->create(['price' => 100]);

        // Set quantity in DB
        \DB::table('invoice_details')->where('id', $detail->id)->update(['quantity' => 3]);

        $detail = $detail->fresh();

        // getTotalPriceAttribute receives null (no total_price column), so result is 0
        $this->assertEquals(0, $detail->total_price);
    }
}
