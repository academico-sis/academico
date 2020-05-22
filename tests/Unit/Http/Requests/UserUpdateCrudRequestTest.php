<?php

namespace Tests\Unit\Http\Requests;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/**
 * @see \App\Http\Requests\UserUpdateCrudRequest
 */
class UserUpdateCrudRequestTest extends TestCase
{
    /** @var \App\Http\Requests\UserUpdateCrudRequest */
    private $subject;

    protected function setUp(): void
    {
        parent::setUp();

        $this->subject = new \App\Http\Requests\UserUpdateCrudRequest();
    }

    /**
     * @test
     */
    public function rules()
    {
        $this->markTestIncomplete('This test case was generated by Shift. When you are ready, remove this line and complete this test case.');

        $actual = $this->subject->rules();

        $this->assertValidationRules([
            'firstname' => 'required',
            'lastname' => 'required',
            'password' => 'confirmed',
        ], $actual);
    }

    // test cases...
}
