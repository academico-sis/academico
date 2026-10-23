<?php

namespace Tests\Feature;

use App\Mail\MonthlyReport;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Tests\TestCase;

class MonthlyReportMailTest extends TestCase
{
    use RefreshDatabase;

    public function test_month_is_named_in_the_institution_language(): void
    {
        Carbon::setTestNow('2026-10-23 10:00:00');
        config(['academico.locale' => 'fr']);
        app()->setLocale('en');

        $mail = new MonthlyReport;

        $this->assertSame('octobre', $mail->monthName);
        $this->assertSame(2026, $mail->year);
        $this->assertSame('en', Carbon::getLocale());
    }
}
