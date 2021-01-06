<?php

namespace Tests\Unit;

use App\Models\Student;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use Tests\TestCase;

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
            'id' => $user->id,
        ]);

        $this->assertEquals(Str::title($user->firstname), $student->firstname);
        $this->assertEquals(Str::upper($user->lastname), $student->lastname);
        $this->assertEquals(Str::title($user->firstname) . ' ' . Str::upper($user->lastname), $student->name);
        $this->assertEquals($user->email, $student->email);
    }
}
