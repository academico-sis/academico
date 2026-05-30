<?php

namespace Tests\Feature;

use App\Livewire\LocaleSwitcher;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\App;
use Livewire\Livewire;
use Tests\TestCase;

class LocaleSwitcherTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        config()->set('app.translatable_locales', ['en', 'es', 'fr', 'pt']);
    }

    public function test_mount_sets_current_locale(): void
    {
        App::setLocale('fr');

        $component = Livewire::test(LocaleSwitcher::class);

        $component->assertSet('currentLocale', 'fr');
    }

    public function test_switch_locale_updates_user_when_authenticated(): void
    {
        $user = User::factory()->create(['locale' => 'en']);
        $this->actingAs($user);

        Livewire::test(LocaleSwitcher::class)
            ->call('switchLocale', 'es');

        $this->assertEquals('es', $user->fresh()->locale);
    }

    public function test_switch_locale_sets_session_for_guest(): void
    {
        Livewire::test(LocaleSwitcher::class)
            ->call('switchLocale', 'fr');

        $this->assertEquals('fr', session('locale'));
    }

    public function test_switch_locale_rejects_invalid_locale(): void
    {
        $component = Livewire::test(LocaleSwitcher::class)
            ->call('switchLocale', 'xx');

        // The locale should not have changed
        $component->assertSet('currentLocale', App::getLocale());
    }

    public function test_switch_locale_updates_app_locale(): void
    {
        Livewire::test(LocaleSwitcher::class)
            ->call('switchLocale', 'es')
            ->assertSet('currentLocale', 'es');
    }
}
