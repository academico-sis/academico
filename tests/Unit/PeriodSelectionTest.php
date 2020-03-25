<?php

namespace Tests\Unit;

use App\Models\Period;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class PeriodSelectionTest extends TestCase
{
    use RefreshDatabase;

    /**
     * If there is a config key and the period exists, return it.
     */
    public function testDefaultPeriodSelectionFromConfig()
    {
        // given two periods
        $period1 = factory(Period::class)->create([
            'start'   => date('Y-m-d', strtotime('-3 months')),
            'end'     => date('Y-m-d', strtotime('-2 months')),
            'year_id' => 1,
            'name'    => 'period 1 past',
        ]);

        $period2 = factory(Period::class)->create([
            'start'   => date('Y-m-d', strtotime('-1 months')),
            'end'     => date('Y-m-d', strtotime('+1 months')),
            'year_id' => 1,
            'name'    => 'period 2',
        ]);

        // config is seeded automatically, so we need to update the record
        DB::table('config')->where('name', 'current_period')
            ->update(['value' => $period2->id]);

        // the period registered in config will be returned
        $this->assertEquals($period2->id, Period::get_default_period()->id);
    }

    /**
     * If the default period cannot be fouund,
     * return the first period which has not yet ended.
     */
    public function testDefaultPeriodFallbackToFirstPeriodNotOver()
    {
        $period1 = factory(Period::class)->create([
            'start'   => date('Y-m-d', strtotime('-3 months')),
            'end'     => date('Y-m-d', strtotime('+2 months')),
            'year_id' => 1,
            'name'    => 'period 1 current',
        ]);

        $period2 = factory(Period::class)->create([
            'start'   => date('Y-m-d', strtotime('+2 months')),
            'end'     => date('Y-m-d', strtotime('+4 months')),
            'year_id' => 1,
            'name'    => 'period 2 future',
        ]);

        DB::table('config')->where('name', 'current_period')->update(['value' => null]);
        Period::first()->delete();

        $this->assertEquals($period1->id, Period::get_default_period()->id);
    }

    /**
     * Similar test for the default enrollment period.
     * We use this period to display the list of available courses to enrol a student
     * If there is a config key and the period exists, return it.
     */
    public function testEnrollmentPeriodSelectionFromConfig()
    {
        // given two periods
        $period1 = factory(Period::class)->create([
            'start'   => date('Y-m-d', strtotime('-3 months')),
            'end'     => date('Y-m-d', strtotime('-2 months')),
            'year_id' => 1,
            'name'    => 'period 1 past',
        ]);

        $period2 = factory(Period::class)->create([
            'start'   => date('Y-m-d', strtotime('-1 months')),
            'end'     => date('Y-m-d', strtotime('+1 months')),
            'year_id' => 1,
            'name'    => 'period 2',
        ]);

        // config is seeded automatically, so we need to update the record
        DB::table('config')->where('name', 'default_enrollment_period')
            ->update(['value' => $period2->id]);

        // the period registered in config will be returned
        $this->assertEquals($period2->id, Period::get_enrollments_period()->id);
    }

    /**
     * If the default enrollment period cannot be fouund,
     * return the first period which has not yet ended.
     */
    public function testEnrollmentPeriodFallbackToFirstPeriodNotOver()
    {
        $period1 = factory(Period::class)->create([
            'start'   => date('Y-m-d', strtotime('-3 months')),
            'end'     => date('Y-m-d', strtotime('+2 months')),
            'year_id' => 1,
            'name'    => 'period 1 current',
        ]);

        $period2 = factory(Period::class)->create([
            'start'   => date('Y-m-d', strtotime('+2 months')),
            'end'     => date('Y-m-d', strtotime('+4 months')),
            'year_id' => 1,
            'name'    => 'period 2 future',
        ]);

        DB::table('config')->where('name', 'default_enrollment_period')->update(['value' => null]);
        Period::first()->delete();

        $this->assertEquals($period1->id, Period::get_enrollments_period()->id);
    }
}
