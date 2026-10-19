<?php

namespace Tests\Feature;

use App\Livewire\RegistrationWizard;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationWizardTest extends TestCase
{
    use RefreshDatabase;

    private function fillWizard(): Testable
    {
        return Livewire::test(RegistrationWizard::class)
            ->set('firstname', 'Jane')
            ->set('lastname', 'Doe')
            ->set('email', 'jane@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->set('gender', 2)
            ->set('idnumber', '123456')
            ->call('nextStep')
            ->set('birthdate', '2000-01-01')
            ->set('address', '1 Main Street')
            ->set('phonenumbers', ['0123456789'])
            ->call('nextStep')
            ->call('nextStep')
            ->call('nextStep');
    }

    public function test_student_can_register_when_no_terms_or_rules_are_configured(): void
    {
        config(['registration.terms_url' => null, 'registration.rules_url' => null]);

        $this->fillWizard()
            ->assertSet('currentStep', 5)
            ->call('register')
            ->assertHasNoErrors()
            ->assertSet('registered', true);

        $user = User::where('email', 'jane@example.com')->firstOrFail();
        $this->assertTrue(Student::whereKey($user->id)->exists());
    }

    public function test_terms_must_be_accepted_when_configured(): void
    {
        config(['registration.terms_url' => 'https://example.com/terms']);

        $this->fillWizard()
            ->call('register')
            ->assertHasErrors(['accept_terms' => 'accepted'])
            ->assertSet('registered', false);

        $this->assertFalse(User::where('email', 'jane@example.com')->exists());
    }
}
