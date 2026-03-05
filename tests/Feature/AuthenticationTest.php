<?php

namespace Tests\Feature;

use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_login(): void
    {
        $response = $this->get('/dashboard');
        $response->assertRedirect('/login');
    }

    public function test_guest_can_access_registration_page(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    public function test_login_route_redirects_to_admin_login(): void
    {
        $response = $this->get('/login');
        $response->assertRedirect('/admin/login');
    }

    public function test_authenticated_student_can_access_dashboard(): void
    {
        $student = Student::factory()->create();
        $user = $student->user;

        $response = $this->actingAs($user)->get('/dashboard');
        // Student pages render Livewire components; verify we're not redirected
        $response->assertSuccessful();
    }

    public function test_authenticated_user_can_access_account_page(): void
    {
        $student = Student::factory()->create();
        $user = $student->user;

        $response = $this->actingAs($user)->get('/account');
        $response->assertSuccessful();
    }

    public function test_authenticated_user_can_logout(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/');

        $this->assertGuest();
    }

    public function test_homepage_is_accessible(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_admin_panel_requires_authentication(): void
    {
        $response = $this->get('/admin');
        $response->assertRedirect();
    }

    public function test_authenticated_user_with_role_can_access_admin_panel(): void
    {
        Role::findOrCreate('admin', 'web');
        $user = User::factory()->create();
        $user->assignRole('admin');

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
    }

    public function test_authenticated_user_without_role_cannot_access_admin_panel(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(403);
    }
}
