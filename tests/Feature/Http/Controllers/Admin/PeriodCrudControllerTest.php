<?php

namespace Tests\Feature\Http\Controllers\Admin;

use App\Models\Period;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\Admin\PeriodCrudController
 */
class PeriodCrudControllerTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('TestSeeder');
        $this->logAdmin();
    }

    /**
     * @test
     */
    public function create_returns_an_ok_response()
    {
        $response = $this->get(route('period.create'));
        $response->assertOk();
    }

    /**
     * @test
     */
    public function destroy_returns_an_ok_response()
    {
        $period = factory(Period::class)->create();
        $response = $this->delete(route('period.destroy', ['id' => $period->id]));
        $response->assertOk();
        $this->assertModelMissing($period);
    }

    /**
     * @test
     */
    public function edit_returns_an_ok_response()
    {
        $period = factory(Period::class)->create();
        $response = $this->get(route('period.edit', ['id' => $period->id]));
        $response->assertOk();
    }

    /**
     * @test
     */
    public function index_returns_an_ok_response()
    {
        $response = $this->get(route('period.index'));

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function search_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->post(route('period.search'), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function show_details_row_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->get(route('period.showDetailsRow', ['id' => $id]));

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function store_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->post(route('period.store'), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    /**
     * @test
     */
    public function update_returns_an_ok_response()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $response = $this->put(route('period.update', ['id' => $id]), [
            // TODO: send request data
        ]);

        $response->assertOk();

        // TODO: perform additional assertions
    }

    // test cases...
}
