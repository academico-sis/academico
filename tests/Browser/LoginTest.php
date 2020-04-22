<?php

namespace Tests\Browser;

use App\Models\Student;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;

    public function setUp(): void
    {
        parent::setUp();
        $this->seed('TestSeeder');
    }

    public function testLoginAsAdmin()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertSee('Iniciar sesión');
            $browser->visit('/')->type('email', 'admin@academico.site')->type('password', 'secret')->press('Iniciar sesión')->assertSee('Bonjour');
            $browser->screenshot('admin-dashboard');
        });
    }

    public function testLoginAsSecretary()
    {
        $user = factory(User::class)->create();
        $user->assignRole('secretary');
        $this->browse(function (Browser $browser) use ($user) {
            $browser->visit('/')->type('email', $user->email)->type('password', 'secret')->press('Iniciar sesión');
            $browser->screenshot('secretary-dashboard');
        });
    }

    public function testLoginAsTeacher()
    {
        $teacher = factory(Teacher::class)->create();

        $this->browse(function (Browser $browser) use ($teacher) {
            $browser->visit('/')->type('email', $teacher->email)->type('password', 'secret')->press('Iniciar sesión');
            $browser->screenshot('teacher-dashboard');
        });
    }

    public function testLoginAsStudent()
    {
        $student = factory(Student::class)->create();
        $this->browse(function (Browser $browser) use ($student) {
            $browser->visit('/')->type('email', $student->email)->type('password', 'secret')->press('Iniciar sesión');
            $browser->screenshot('student-dashboard');
        });
    }
}
