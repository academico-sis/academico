<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class EnrollmentViewAuthTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed('TestSeeder');
    }

    /** @test **/
    public function unathorized_users_cannot_view_enrollments()
    {
        $response = $this->get('enrollment');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test **/
    public function teachers_cannot_view_enrollments()
    {
        $teacher = factory(Teacher::class)->create();
        backpack_auth()->login($teacher->user, true);

        $response = $this->get('enrollment');
        $response->assertStatus(403);
    }

    /** @test **/
    public function students_cannot_view_enrollments()
    {
        $student = factory(Student::class)->create();
        backpack_auth()->login($student->user, true);

        $response = $this->get('enrollment');
        $response->assertStatus(403);
    }

    /** @test **/
    public function admins_may_view_enrollments()
    {
        $user = factory(User::class)->create();
        $user->assignRole('admin');

        \Auth::guard(backpack_guard_name())->login($user);

        $response = $this->get('enrollment');
        $response->assertOk();
    }

    /** @test **/
    public function secreteries_may_view_enrollments()
    {
        $user = factory(User::class)->create();
        $user->assignRole('secretary');

        \Auth::guard(backpack_guard_name())->login($user);

        $response = $this->get('enrollment');
        $response->assertOk();
    }
}
