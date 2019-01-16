<?php

namespace Tests\Browser;

use Tests\DuskTestCase;
use Laravel\Dusk\Browser;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use App\Models\User;

class ExampleTest extends DuskTestCase
{
    use DatabaseMigrations;
    /**
     * A basic browser test example.
     *
     * @return void
     */
    public function testBasicExample()
    {

        //$this->seed('DatabaseSeeder');
        
        // create an admin user and log them in to access protected routes
        $admin = factory(User::class)->create();
        //$admin->assignRole('admin');
        backpack_auth()->login($admin, true);

        //dd(backpack_auth()->user());
        $this->browse(function (Browser $browser) {
            $response = $browser->visit('/')->assertPathIs('/');;
            $response->driver->takeScreenshot(base_path('tests/Browser/screenshots/testLogin.png'));
        });
    }
}
