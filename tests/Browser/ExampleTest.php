<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;

class ExampleTest extends DuskTestCase
{
    //use DatabaseMigrations;


    /**
     * Login as an admin and take a screenshot of the dashboard.
     */
    public function testAdminDashboard()
    {

        //$this->seed('DatabaseSeeder');
        
        // create an admin user and log them in to access protected routes
        //$admin = factory(User::class)->create();
        //$admin->assignRole('admin');

        $this->browse(function (Browser $browser) use ($admin) {
            $browser->visit('/login')
                    ->type('email', $admin->email)
                    ->type('password', 'secret')
                    ->press('Connexion')
                    ->screenshot('admin-dashboard');
        });
    }


    public function testTeacherDashboard()
    {
        //$this->seed('DatabaseSeeder');

        // create an admin user and log them in to access protected routes
        //$teacher = factory(User::class)->create();
        //$teacher->assignRole('teacher');

        $this->browse(function (Browser $browser) use ($teacher) {
            $browser->visit('/login')
                    ->type('email', $teacher->email)
                    ->type('password', 'secret')
                    ->press('Connexion')
                    ->screenshot('teacher-dashboard');
        });
    }


}


