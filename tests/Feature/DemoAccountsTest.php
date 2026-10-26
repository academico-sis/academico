<?php

namespace Tests\Feature;

use App\Filament\Auth\Login;
use App\Services\Settings;
use Database\Seeders\TestSeeder;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class DemoAccountsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Filament::setCurrentPanel('admin');
    }

    public function test_the_login_page_lists_no_account_by_default(): void
    {
        $this->get('/admin/login')->assertSuccessful()->assertDontSee(__('Demo accounts'))->assertDontSee(__('Public demo'));
    }

    public function test_the_login_page_warns_demo_visitors_and_links_the_mailbox(): void
    {
        app(Settings::class)->set(['demo_accounts' => [['role' => 'Administrator', 'email' => 'admin@example.org', 'password' => 'secret']]]);

        $this->get('/admin/login')
            ->assertSee(__('Public demo'))
            ->assertSee(__('Anyone can see what you enter here. Do not enter real names, email addresses or other personal data.'))
            ->assertDontSee(__('Open the demo mailbox'));

        config(['services.demo_mailbox.url' => 'https://mail.demo.example.org']);

        $this->get('/admin/login')
            ->assertSee(__('Open the demo mailbox'))
            ->assertSee('href="https://mail.demo.example.org"', false);
    }

    public function test_the_login_page_lists_the_demo_accounts_and_fills_the_form(): void
    {
        app(Settings::class)->set(['demo_accounts' => [
            ['role' => 'Administrator', 'email' => 'admin@example.org', 'password' => 'secret'],
            ['role' => 'Teacher', 'email' => 'teacher@example.org', 'password' => 'secret'],
        ]]);

        $this->get('/admin/login')
            ->assertSuccessful()
            ->assertSee(__('Demo accounts'))
            ->assertSee('teacher@example.org');

        Livewire::test(Login::class)
            ->call('useDemoAccount', 1)
            ->assertSet('data.email', 'teacher@example.org')
            ->assertSet('data.password', 'secret')
            ->call('useDemoAccount', 9)
            ->assertSet('data.email', 'teacher@example.org');
    }

    public function test_the_demo_seeder_lists_an_account_per_role_that_can_log_in(): void
    {
        $this->seed(TestSeeder::class);

        $accounts = app(Settings::class)->get('demo_accounts');
        $this->assertSame(['Administrator', 'Secretary', 'Teacher', 'Student'], array_column($accounts, 'role'));

        foreach ($accounts as $account) {
            $this->assertTrue(auth()->validate(['email' => $account['email'], 'password' => $account['password']]), $account['email']);
        }
    }
}
