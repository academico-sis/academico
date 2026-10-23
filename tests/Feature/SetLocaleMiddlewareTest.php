<?php

namespace Tests\Feature;

use App\Http\Middleware\SetLocale;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Tests\TestCase;

class SetLocaleMiddlewareTest extends TestCase
{
    use RefreshDatabase;

    private function dispatchMiddleware(Request $request): void
    {
        $middleware = new SetLocale;
        $middleware->handle($request, function () {
            return response('ok');
        });
    }

    public function test_authenticated_user_locale_is_applied(): void
    {
        $user = User::factory()->create(['locale' => 'fr']);

        $this->actingAs($user);

        $request = Request::create('/test');
        $request->setLaravelSession(app('session.store'));

        $this->dispatchMiddleware($request);

        $this->assertEquals('fr', App::getLocale());
    }

    public function test_session_locale_used_for_guest(): void
    {
        $session = app('session.store');
        $session->put('locale', 'es');

        $request = Request::create('/test');
        $request->setLaravelSession($session);

        $this->dispatchMiddleware($request);

        $this->assertEquals('es', App::getLocale());
    }

    public function test_visitor_without_preference_gets_the_institution_language(): void
    {
        config()->set('academico.locale', 'es');

        $request = Request::create('/test');
        $request->headers->remove('Accept-Language');
        $request->setLaravelSession(app('session.store'));

        $this->dispatchMiddleware($request);

        $this->assertEquals('es', App::getLocale());
    }

    public function test_browser_language_wins_over_the_institution_language(): void
    {
        config()->set('academico.locale', 'es');

        $request = Request::create('/test', server: ['HTTP_ACCEPT_LANGUAGE' => 'fr-FR,fr;q=0.9']);
        $request->setLaravelSession(app('session.store'));

        $this->dispatchMiddleware($request);

        $this->assertEquals('fr', App::getLocale());
    }

    public function test_unsupported_browser_language_gets_the_institution_language(): void
    {
        config()->set('academico.locale', 'pt');

        $request = Request::create('/test', server: ['HTTP_ACCEPT_LANGUAGE' => 'de-DE,de;q=0.9']);
        $request->setLaravelSession(app('session.store'));

        $this->dispatchMiddleware($request);

        $this->assertEquals('pt', App::getLocale());
    }

    public function test_user_locale_overrides_session(): void
    {
        $user = User::factory()->create(['locale' => 'fr']);

        $this->actingAs($user);

        $session = app('session.store');
        $session->put('locale', 'es');

        $request = Request::create('/test');
        $request->setLaravelSession($session);

        $this->dispatchMiddleware($request);

        $this->assertEquals('fr', App::getLocale());
    }

    public function test_carbon_locale_is_set(): void
    {
        $user = User::factory()->create(['locale' => 'es']);

        $this->actingAs($user);

        $request = Request::create('/test');
        $request->setLaravelSession(app('session.store'));

        $this->dispatchMiddleware($request);

        $this->assertEquals('es', Carbon::getLocale());
    }
}
