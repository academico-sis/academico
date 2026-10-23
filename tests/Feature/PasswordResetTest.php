<?php

namespace Tests\Feature;

use App\Models\User;
use Filament\Auth\Notifications\ResetPassword as ResetPasswordNotification;
use Filament\Auth\Pages\PasswordReset\RequestPasswordReset;
use Filament\Auth\Pages\PasswordReset\ResetPassword;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');
    }

    public function test_staff_member_can_reset_their_password(): void
    {
        Notification::fake();
        $user = User::factory()->create(['email' => 'jane@example.com']);
        $user->assignRole(Role::create(['name' => 'secretary']));

        Livewire::test(RequestPasswordReset::class)
            ->fillForm(['email' => 'jane@example.com'])
            ->call('request')
            ->assertHasNoFormErrors();

        $token = null;
        Notification::assertSentTo($user, ResetPasswordNotification::class, function (ResetPasswordNotification $notification) use (&$token): bool {
            $token = $notification->token;

            return true;
        });
        $this->assertDatabaseHas('password_reset_tokens', ['email' => 'jane@example.com']);

        Livewire::test(ResetPassword::class, ['email' => 'jane@example.com', 'token' => $token])
            ->fillForm(['password' => 'new-password-123', 'passwordConfirmation' => 'new-password-123'])
            ->call('resetPassword')
            ->assertHasNoFormErrors();

        $this->assertTrue(Hash::check('new-password-123', $user->fresh()->password));
    }
}
