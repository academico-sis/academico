<?php

namespace Tests\Feature;

use App\Livewire\StudentAccount;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Livewire\Features\SupportLockedProperties\CannotUpdateLockedPropertyException;
use Livewire\Livewire;
use Tests\TestCase;

class StudentAccountTest extends TestCase
{
    use RefreshDatabase;

    public function test_force_update_redirects_student_to_account(): void
    {
        $student = Student::factory()->create(['force_update' => true]);
        $user = $student->user;

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertRedirect(route('student.account'));
    }

    public function test_force_update_allows_access_to_account_page(): void
    {
        $student = Student::factory()->create(['force_update' => true]);
        $user = $student->user;

        $response = $this->actingAs($user)->get('/account');
        $response->assertStatus(200);
    }

    public function test_force_update_allows_post_requests(): void
    {
        $student = Student::factory()->create(['force_update' => true]);
        $user = $student->user;

        // POST requests should pass through even with force_update
        $response = $this->actingAs($user)->post('/logout');
        $response->assertRedirect('/');
    }

    public function test_non_force_update_student_can_access_dashboard(): void
    {
        $student = Student::factory()->create(['force_update' => false]);
        $user = $student->user;

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
    }

    public function test_users_who_are_not_students_are_sent_away_from_the_student_pages(): void
    {
        foreach ([User::factory()->create(), Teacher::factory()->create()->user] as $user) {
            $this->actingAs($user)->get('/dashboard')->assertRedirect(route('home'));
            $this->actingAs($user)->get('/account')->assertRedirect(route('home'));
        }

        $this->assertFalse(Student::exists());
    }

    public function test_student_dashboard_shows_enrollments(): void
    {
        $student = Student::factory()->create();
        $user = $student->user;

        $response = $this->actingAs($user)->get('/dashboard');
        $response->assertStatus(200);
        $response->assertSee(__('Dashboard'));
    }

    public function test_student_account_page_shows_account_tabs(): void
    {
        $student = Student::factory()->create();
        $user = $student->user;

        $response = $this->actingAs($user)->get('/account');
        $response->assertStatus(200);
    }

    public function test_student_cannot_set_a_birthdate_in_the_future(): void
    {
        $student = Student::factory()->create();

        Livewire::actingAs($student->user)
            ->test(StudentAccount::class)
            ->set('idnumber', '123456')
            ->set('address', '1 Main Street')
            ->set('birthdate', today()->addDay()->toDateString())
            ->call('saveStudentInfo')
            ->assertHasErrors(['birthdate' => 'before_or_equal']);
    }

    public function test_student_info_is_saved_on_the_student_record(): void
    {
        $student = Student::factory()->create();

        Livewire::actingAs($student->user)
            ->test(StudentAccount::class)
            ->set('idnumber', '123456')
            ->set('address', '1 Main Street')
            ->set('birthdate', '2000-01-01')
            ->call('saveStudentInfo')
            ->assertHasNoErrors();

        $this->assertSame('1 Main Street', $student->fresh()->address);
        $this->assertSame(1, Student::count());
    }

    public function test_forced_update_cannot_be_skipped_from_the_browser(): void
    {
        $student = Student::factory()->create(['force_update' => 1]);

        $this->expectException(CannotUpdateLockedPropertyException::class);

        Livewire::actingAs($student->user)->test(StudentAccount::class)->set('forceUpdate', null);
    }

    public function test_changing_the_password_requires_the_current_password(): void
    {
        $student = Student::factory()->create();

        Livewire::actingAs($student->user)
            ->test(StudentAccount::class)
            ->set('current_password', 'wrong-password')
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('savePassword')
            ->assertHasErrors(['current_password' => 'current_password'])
            ->set('current_password', 'password')
            ->set('password', 'short')
            ->set('password_confirmation', 'short')
            ->call('savePassword')
            ->assertHasErrors(['password'])
            ->set('password', 'new-password')
            ->set('password_confirmation', 'new-password')
            ->call('savePassword')
            ->assertHasNoErrors();

        $this->assertTrue(Hash::check('new-password', $student->user->fresh()->password));
    }
}
