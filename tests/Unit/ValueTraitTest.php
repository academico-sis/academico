<?php

namespace Tests\Unit;

use App\Models\Payment;
use App\Models\ScheduledPayment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ValueTraitTest extends TestCase
{
    use RefreshDatabase;

    public function test_value_getter_divides_by_100(): void
    {
        $payment = Payment::factory()->create(['value' => 150]);

        $this->assertEquals(150, $payment->value);
        // The factory sets value = 150, the setter multiplies by 100 → DB has 15000
        // The getter divides by 100 → returns 150
    }

    public function test_value_setter_multiplies_by_100(): void
    {
        $payment = Payment::factory()->create(['value' => 250]);

        $rawValue = \DB::table('payments')->where('id', $payment->id)->value('value');

        $this->assertEquals(25000, $rawValue);
    }

    public function test_value_with_currency_before(): void
    {
        config()->set('academico.currency_position', 'before');
        config()->set('academico.currency_symbol', '$');

        $scheduledPayment = ScheduledPayment::factory()->create(['value' => 75]);

        $this->assertEquals('$ 75', $scheduledPayment->value_with_currency);
    }

    public function test_value_with_currency_after(): void
    {
        config()->set('academico.currency_position', 'after');
        config()->set('academico.currency_symbol', '€');

        $scheduledPayment = ScheduledPayment::factory()->create(['value' => 100]);

        $this->assertEquals('100 €', $scheduledPayment->value_with_currency);
    }
}
