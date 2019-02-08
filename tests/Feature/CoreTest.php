<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CoreTest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function test_that_a_guest_user_is_redirected_to_login_page()
    {
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertSee('login');
    }
}
