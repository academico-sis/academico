<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Period;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class CoreTest extends TestCase
{
    use DatabaseMigrations;
    /**
     * Many methods will require a default current period
     *
     * @return void
     */
    public function test_that_there_exists_a_current_period()
    {
        factory(Period::class)->create();
        $default_period = Period::get_default_period();
        $this->assertTrue($default_period->exists());
    }
}
