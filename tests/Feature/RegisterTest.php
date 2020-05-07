<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

/** You need to set BACKPACK_REGISTRATION_OPEN=true in your .env file for this test to work */
class RegisterTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp(): void
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
    }

    public function test_that_a_student_is_created()
    {
    }

    /**
     * Check that validation rules prevent incomplete data to be submitted to the DB.
     * @test
     */
    public function testUserCreationValidationRules()
    {
        // when we post failing data to the endpoint
        // Assert that the errors are returned
    }
}
