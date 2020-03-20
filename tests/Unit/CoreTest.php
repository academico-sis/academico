<?php

namespace Tests\Unit;

use App\Models\Period;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        //$this->seed('DatabaseSeeder');
    }

    /** @test */
    public function there_exists_a_current_period()
    {
        $period = factory(Period::class)->create();
        $this->assertTrue(Period::get_default_period()->exists());
    }
}
