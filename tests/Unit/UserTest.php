<?php

namespace Tests\Unit;

use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use Illuminate\Support\Facades\App;

class UserTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function access_student_birthdate()
    {
        App::setLocale('en');
        // create a student
        $student = factory(Student::class)->create([
            'birthdate' => '2000-03-25',
        ]);

        $this->assertEquals('March 25, 2000', $student->student_birthdate);
    }

    /** @test */
    public function access_student_age()
    {
        // create a student
        $student = factory(Student::class)->create([
            'birthdate' => Carbon::parse('128 months ago'),
        ]);

        $this->assertEquals('10', $student->student_age);
    }

    /** @test */
    public function access_student_base_information()
    {
        // create a user
        $user = factory(User::class)->create();

        // create a student corresponding to this User
        $student = factory(Student::class)->create([
            'user_id' => $user->id,
        ]);

        $this->assertEquals($user->firstname, $student->firstname);
        $this->assertEquals($user->lastname, $student->lastname);
        $this->assertEquals($user->firstname.' '.$user->lastname, $student->name);
        $this->assertEquals($user->email, $student->email);
    }
}
