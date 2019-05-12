<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Student;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class RegisterTest extends TestCase
{
    use RefreshDatabase;
    use WithFaker;

    public function setUp()
    {
        parent::setUp();
        
        $this->seed('DatabaseSeeder');
    }

    /**
     * Check that the registration form is available
     *
     * @test
     */
    public function RegisterationPageIsAvailable()
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /**
     * Check that a user is created with the form action endpoint
     * @test
     */
    public function testIfUserIsCreated()
    {
        $firstname = $this->faker->firstName();
        $lastname = $this->faker->lastName();
        $email = $this->faker->unique()->safeEmail;
        $address = $this->faker->address();
        $idnumber = $this->faker->randomNumber();
        $phone = $this->faker->e164PhoneNumber();
        $birthdate = $this->faker->date();
        $genre_id = $this->faker->numberBetween($min = 0, $max = 2);

        // when we post data to the endpoint
        $response = $this->json('POST', route('backpack.auth.register'), [
            'firstname' => $firstname,
            'lastname' => $lastname,
            'idnumber' => $idnumber,
            'genre_id' => $genre_id,
            'birthdate' => $birthdate,
            'address' => $address,
            'phone_number' => $phone,
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
        $user = $user->first();

        // with associated student (return response)
        $student = Student::where('user_id', $user->id);

        $this->assertTrue($student->count() > 0);
        $student = $student->first();

        $this->assertEquals($student->idnumber, $idnumber);
        $this->assertEquals($student->birthdate, $birthdate);
        $this->assertEquals($student->address, $address);
        $this->assertEquals($student->genre_id, $genre_id);
        $this->assertTrue($student->phone->contains('phone_number', $phone));

    }
}
