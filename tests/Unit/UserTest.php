<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use App\Models\User;
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
            'birthdate' => Carbon::parse("120 months ago"),
        ]);
        $age = $user->student_age;
        $this->assertEquals('10', $age);
    }
}
