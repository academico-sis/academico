<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CoreTest extends TestCase
{
    /** @test */
    public function guest_users_are_redirected_to_login_page()
    {
        $response = $this->get('/');
        $response->assertStatus(302);
        $response->assertSee('login');
    }
}
