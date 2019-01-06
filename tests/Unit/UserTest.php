<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
use App\Models\UserData;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserTest extends TestCase
{
    use DatabaseMigrations;
   
    public function test_that_a_user_birthdate_can_be_accessed()
    {
        $user = factory(User::class)->create([
            'birthdate' => '2000-03-25',
        ]);
        $birthdate = $user->student_birthdate;
        $this->assertEquals("Mar 25, 2000", $birthdate);
    }

    public function test_that_a_user_age_can_be_accessed()
    {
        $user = factory(User::class)->create([
            'birthdate' => Carbon::parse("128 months ago"),
        ]);
        $age = $user->student_age;
        $this->assertEquals('10', $age);
    }

    public function test_student_scope()
    {
        $this->seed('DatabaseSeeder');

        // given two new users
        $student = factory(User::class)->create();
        $teacher = factory(User::class)->create();

        // one user with a student role
        $student->assignRole('student');
        
        // the other user with a teacher role
        $teacher->assignRole('teacher');
        
        // users are filtered accordingly
        $users = User::student();
        $this->assertFalse($users->contains($teacher->id));

        $users = User::teacher();
        $this->assertFalse($users->contains($student->id));
    }

    public function test_that_a_user_additional_contacts_are_returned()
    {
        // given a user
        $student = factory(User::class)->create();

        // with two additional contacts

        $contact1 = factory(UserData::class)->create(
            ['user_id' => $student->id ]
        );

        $contact2 = factory(UserData::class)->create(
            ['user_id' => $student->id ]
        );

        // the name of the two contacts are returned
        $this->assertTrue($student->additional_data->contains($contact1));
        $this->assertTrue($student->additional_data->contains($contact2));
    }
}
