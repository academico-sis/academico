<?php

namespace Tests\Unit;

use App\Models\Student;
use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\Contact;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;
   
    public function test_that_a_user_birthdate_can_be_accessed()
    {
        // create a student
        $user = factory(User::class)->create();

        $student = factory(Student::class)->create([
            'user_id' => $user->id,
            'birthdate' => '2000-03-25',
        ]);

        $birthdate = $student_birthdate;
        $this->assertEquals("Mar 25, 2000", $birthdate);
    }

    public function test_that_a_user_age_can_be_accessed()
    {
        // create a student
        $user = factory(User::class)->create();

        $student = factory(Student::class)->create([
            'user_id' => $user->id,
            'birthdate' => Carbon::parse("128 months ago"),
        ]);

        $age = $student_age;
        $this->assertEquals('10', $age);
    }


    public function test_add_additional_contact_to_user()
    {
        // Arrange: given a user
        $user = factory(User::class)->create();

        $student = factory(Student::class)->create([
            'user_id' => $user->id,
        ]);

        \Auth::guard(backpack_guard_name())->login($user);

        // Act: send additional contact data

        $this->json('POST', route('addContact'), [
            'student_id' => $student->id,
            'firstname' => "Eva",
            'lastname' => "Verdo",
            'email' => "evita@example.com",
            'address' => "example 123 address",
            'idnumber' => "65656565FGFGFG"
        ]);

        // Assert: verify that the additional contacts data are linked to the student
        $this->assertTrue($student->contacts->first()['idnumber'] == "65656565FGFGFG");
        $this->assertTrue($student->contacts->first()['email'] == "evita@example.com");
    }

}
