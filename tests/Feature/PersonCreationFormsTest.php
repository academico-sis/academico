<?php

namespace Tests\Feature;

use App\Filament\Resources\Students\Pages\CreateStudent;
use App\Filament\Resources\Teachers\Pages\CreateTeacher;
use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Database\Seeders\PermissionsSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class PersonCreationFormsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->seed(PermissionsSeeder::class);

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
    }

    public function test_teachers_with_the_same_name_can_be_created(): void
    {
        foreach (['jane1@example.com', 'jane2@example.com'] as $email) {
            Livewire::test(CreateTeacher::class)
                ->fillForm(['firstname' => 'Jane', 'lastname' => 'Doe', 'email' => $email])
                ->call('create')
                ->assertHasNoFormErrors();
        }

        $this->assertEquals(2, Teacher::whereHas('user', fn ($query) => $query->where('lastname', 'Doe'))->count());
    }

    public function test_students_with_the_same_name_can_be_created(): void
    {
        foreach ([1, 2] as $attempt) {
            Livewire::test(CreateStudent::class)
                ->fillForm(['firstname' => 'Jane', 'lastname' => 'Doe', 'gender_id' => 1])
                ->call('create')
                ->assertHasNoFormErrors();
        }

        $this->assertEquals(2, Student::whereHas('user', fn ($query) => $query->where('lastname', 'Doe'))->count());
    }

    public function test_teacher_weekly_hours_cannot_be_negative(): void
    {
        Livewire::test(CreateTeacher::class)
            ->fillForm(['firstname' => 'Jane', 'lastname' => 'Doe', 'email' => 'jane@example.com', 'max_week_hours' => -20])
            ->call('create')
            ->assertHasFormErrors(['max_week_hours' => 'min']);

        $this->assertFalse(User::where('email', 'jane@example.com')->exists());
    }

    public function test_student_birthdate_cannot_be_in_the_future(): void
    {
        Livewire::test(CreateStudent::class)
            ->fillForm(['firstname' => 'Jane', 'lastname' => 'Doe', 'gender_id' => 1, 'birthdate' => today()->addDay()->toDateString()])
            ->call('create')
            ->assertHasFormErrors(['birthdate']);

        $this->assertFalse(User::where('lastname', 'Doe')->exists());
    }
}
