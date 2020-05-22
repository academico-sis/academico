<?php

namespace Tests\Feature\Http\Controllers;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\SetupController
 */
class SetupControllerTest extends TestCase
{
    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->get(route('setupHome'));

        $response->assertOk();
        $response->assertViewIs('setup.dashboard');
        $response->assertViewHas('queue');
        $response->assertViewHas('failed');
        $response->assertViewHas('lead_types');
        $response->assertViewHas('orphan_students');
        $response->assertViewHas('currentPeriod');
        $response->assertViewHas('enrollmentsPeriod');
        $response->assertViewHas('uptime');

        // TODO: perform additional assertions
    }

    // test cases...
}
