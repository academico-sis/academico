<?php

namespace Tests\Browser;

use App\Models\User;
use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class LoginTest extends DuskTestCase
{
    use DatabaseMigrations;


    public function testGuestRedirect()
    {
        // a guest user is redirected to the login page
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertUrlIs('http://localhost:8000/admin/login');
        });

    }

    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testUserLoginLogout()
    {

        // a user can log in and is redirected to the homepage
        $user = factory(User::class)->create([
            'email' => 'test@academico.com',
        ]);

        //dd($user);
        $this->browse(function ($browser) use ($user) {
            $browser->visit('/')
                    ->type('email', $user->email)
                    ->type('password', 'secret')
                    ->press('Connexion');
            
            //$browser->screenshot();
            $browser->assertSee('Tableau de bord');
        });

        // the user can log out
        $this->browse(function ($browser) use ($user) {
            $browser->click('.fa-btn .fa-sign-out');
        });

        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                    ->assertUrlIs('http://localhost:8000/admin/login');
        });
        
    }
}
