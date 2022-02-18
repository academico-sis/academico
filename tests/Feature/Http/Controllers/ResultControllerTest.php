<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Enrollment;
use App\Models\Result;
use App\Models\ResultType;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * @see \App\Http\Controllers\ResultController
 */
class ResultControllerTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->setSharedVariables();
        $this->seed('TestSeeder');
    }

    /** @test */
    public function can_add_a_result_for_the_enrollment()
    {
        $this->logAdmin();
        $enrollment = factory(Enrollment::class)->create();
        $resultType = factory(ResultType::class)->create();
        $this->assertNull($enrollment->result);

        $response = $this->post(route('storeResult'), [
            'enrollment' => $enrollment->id,
            'result' => $resultType->id,
        ]);

        $response->assertCreated();
        $this->assertNotNull($enrollment->fresh()->result);
    }

    /** @test */
    public function can_edit_a_result_for_the_enrollment()
    {
        $this->logAdmin();
        $enrollment = factory(Enrollment::class)->create();
        $resultType = factory(ResultType::class)->create();
        factory(Result::class)->create([
            'enrollment_id' => $enrollment->id,
            'result_type_id' => $resultType->id,
        ]);
        $this->assertEquals($enrollment->result->result_type_id, $resultType->id);
        $newResultType = factory(ResultType::class)->create();

        $response = $this->post(route('storeResult'), [
            'enrollment' => $enrollment->id,
            'result' => $newResultType->id,
        ]);

        $response->assertOk();
        $this->assertEquals($enrollment->fresh()->result->result_type_id, $newResultType->id);
    }
}
