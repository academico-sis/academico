<?php

namespace Tests\Feature;

use App\Livewire\RegistrationWizard;
use App\Models\Student;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Tests\TestCase;

class RegistrationWizardTest extends TestCase
{
    use RefreshDatabase;

    private function fillWizard(string $birthdate = '2000-01-01'): Testable
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
            ->set('birthdate', $birthdate)
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

    public function test_email_already_registered_is_refused(): void
    {
        User::factory()->create(['email' => 'jane@example.com']);

        Livewire::test(RegistrationWizard::class)
            ->set('firstname', 'Jane')
            ->set('lastname', 'Doe')
            ->set('email', 'jane@example.com')
            ->set('password', 'secret123')
            ->set('password_confirmation', 'secret123')
            ->set('gender', 2)
            ->call('nextStep')
            ->assertHasErrors(['email' => 'unique'])
            ->assertSet('currentStep', 1);
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

    public function test_birthdate_cannot_be_in_the_future(): void
    {
        $this->fillWizard(birthdate: today()->addDay()->toDateString())
            ->assertHasErrors(['birthdate' => 'before_or_equal'])
            ->assertSet('currentStep', 2);
    }

    public function test_registration_validates_every_step_again(): void
    {
        $this->fillWizard()
            ->assertSet('currentStep', 5)
            ->set('email', 'not-an-email')
            ->set('password', '1')
            ->set('password_confirmation', '1')
            ->set('phonenumbers', [])
            ->call('register')
            ->assertHasErrors(['email', 'password', 'phonenumbers'])
            ->assertSet('registered', false);

        $this->assertFalse(User::exists());
    }

    public function test_wizard_state_and_settings_cannot_be_changed_by_the_browser(): void
    {
        config(['registration.terms_url' => 'https://example.com/terms', 'registration.picture.mandatory' => true]);

        $locked = ['currentStep' => 5, 'totalSteps' => 1, 'registered' => true, 'pictureAllowed' => false, 'pictureMandatory' => false, 'termsUrl' => null, 'rulesUrl' => null];

        foreach ($locked as $property => $value) {
            try {
                Livewire::test(RegistrationWizard::class)->set($property, $value);
                $this->fail("The browser can change {$property}.");
            } catch (CannotUpdateLockedPropertyException) {
                $this->addToAssertionCount(1);
            }
        }
    }

    public function test_password_must_have_at_least_eight_characters(): void
    {
        Livewire::test(RegistrationWizard::class)
            ->set('firstname', 'Jane')
            ->set('lastname', 'Doe')
            ->set('email', 'jane@example.com')
            ->set('password', 'secret1')
            ->set('password_confirmation', 'secret1')
            ->set('gender', 2)
            ->set('idnumber', '123456')
            ->call('nextStep')
            ->assertHasErrors(['password'])
            ->assertSet('currentStep', 1);
    }
}
