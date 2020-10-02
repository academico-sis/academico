<?php

namespace Tests\Feature;

use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CoreTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->seed('TestSeeder');
    }

    /** @test */
    public function guest_users_are_redirected_to_login_page()
    {
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertRedirect('/login');
    }

    /** @test */
    public function guest_may_log_in()
    {
        $response = $this->post('/login', [
            'username' => '',
            'password' => '',
        ]);

        $response->assertStatus(302);
        $response->assertRedirect('/');
    }

    /** @test */
    public function admin_users_are_redirected_to_admin_panel()
    {
        $user = factory(User::class)->create();
        $user->assignRole('admin');

        \Auth::guard(backpack_guard_name())->login($user);

        $response = $this->get('/');
        $response->assertRedirect('/admin');
    }

    /** @test */
    public function teachers_are_redirected_to_teacher_panel()
    {
        $user = factory(Teacher::class)->create();

        \Auth::guard(backpack_guard_name())->login($user->user);

        $response = $this->get('/');
        $response->assertRedirect('/dashboard/teacher');
    }
}
