<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Course;
use App\Models\Period;
use App\Models\GradeType;
use Illuminate\Support\Facades\Auth;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class GradesTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp()
    {
        parent::setUp();

        $this->seed('DatabaseSeeder');

        $user = factory(User::class)->create();
        $user->givePermissionTo('grades.edit');
        \Auth::guard(backpack_guard_name())->login($user);

        // create a fake course
        $this->course = factory(Course::class)->create();

        // enable grades-based evaluation for this course
        $this->course->evaluation_type()->attach(1);
    }


    public function test_adding_a_new_gradetype_to_course()
    {
        $gradetype = GradeType::create([
            'name' => 'writing',
            'total' => 20,
        ]);

        // act: add a new gradetype to the course
        $this->course->grade_type()->attach($gradetype->id);

        // assert: the course now has this gradetype
        $this->assertTrue($this->course->grade_type->contains('name', 'writing'));
    }

    /**
     * Check if a course created within the current period is visible in the course management panel.
     */
    public function test_that_authorized_user_can_view_courses_within_other_periods()
    {
        
    }

    /**
     * Ensure that courses administration is not accessible to users who don't have permission
     */
    public function test_that_unauthroized_user_cannot_view_courses()
    {

    }
}
