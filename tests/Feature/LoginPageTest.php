<?php

namespace Tests\Feature;

use App\Filament\Auth\Login;
use App\Models\Student;
use App\Models\User;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class LoginPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');
    }

    private function login(User $user, string $password = 'password')
    {
        return Livewire::test(Login::class)
            ->fillForm(['email' => $user->email, 'password' => $password])
            ->call('authenticate');
    }

    public function test_staff_member_is_redirected_to_the_panel(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'admin']));

        $this->login($user)->assertHasNoFormErrors()->assertRedirect(Filament::getUrl());

        $this->assertAuthenticatedAs($user);
    }

    public function test_student_is_redirected_to_the_student_dashboard(): void
    {
        $user = Student::factory()->create()->user;

        $this->login($user)->assertHasNoFormErrors()->assertRedirect(route('student.dashboard'));

        $this->assertAuthenticatedAs($user);
    }

    public function test_wrong_password_is_rejected(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::create(['name' => 'admin']));

        $this->login($user, 'wrong-password')->assertHasFormErrors(['email']);

        $this->assertGuest();
    }

    public function test_user_without_panel_access_gets_the_same_error_as_a_wrong_password(): void
    {
        $user = User::factory()->create();

        $this->login($user)->assertHasFormErrors(['email']);

        $this->assertGuest();
    }
}
