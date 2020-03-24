<?php

namespace Tests\Feature;

use App\Models\Student;
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
        $firstname = $this->faker->firstName();
        $lastname = $this->faker->lastName();
        $email = $this->faker->unique()->safeEmail;
        /* $address = $this->faker->address();
        $idnumber = $this->faker->randomNumber();
        $phone = $this->faker->e164PhoneNumber();
        $birthdate = $this->faker->date();
        $genre_id = $this->faker->numberBetween($min = 0, $max = 2); */

        // when we post data to the endpoint
        $response = $this->json('POST', route('backpack.auth.register'), [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'email' => $email,
            'password' => 'secret',
            'password_confirmation' => 'secret',
            'rules' => true,
            ]);

        // Assert that the user is created.
        $user = User::where('firstname', $firstname)
        ->where('lastname', $lastname)
        ->where('email', $email);

        $this->assertTrue($user->count() > 0);
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
        $response = $this->json('POST', route('backpack.auth.register'), [
            'firstname' => '',
            'lastname' => '',
            'email' => '123',
            'password' => 'secret',
            'password_confirmation' => 'secret',
            ]);

        // Assert that the errors are returned
        $response->assertJsonValidationErrors(['firstname', 'lastname', 'email', 'rules']);
    }
}
