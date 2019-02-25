<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Period;
use Illuminate\Foundation\Testing\RefreshDatabase;
class CoreTest extends TestCase
{
    use RefreshDatabase;

    public function setUp()
    {
        parent::setUp();
        $this->seed('DatabaseSeeder');
    }

    /** @test */
    public function there_exists_a_current_period()
    {
        factory(Period::class)->create();
        $this->assertTrue(Period::get_default_period()->exists());
    }
}
