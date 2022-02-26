<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/** You need to set BACKPACK_REGISTRATION_OPEN=true in your .env file for this test to work */
class RegisterTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestSeeder');
    }

    /**
     * Check that the registration form is available.
     *
     * @test
     */
    public function RegisterationPageIsAvailable()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /**
     * Check that a user is created with the form action endpoint.
     * @test
     */
    public function testIfUserIsCreated()
    {
        $this->markTestIncomplete('Test unfinished');
    }

    public function test_that_a_student_is_created()
    {
        $this->markTestIncomplete('Test unfinished');
    }

    /**
     * Check that validation rules prevent incomplete data to be submitted to the DB.
     * @test
     */
    public function testUserCreationValidationRules()
    {
        // when we post failing data to the endpoint
        // Assert that the errors are returned
        $this->markTestIncomplete('Test unfinished');
    }
}
