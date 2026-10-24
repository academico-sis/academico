<?php

namespace Tests\Feature;

use App\Http\Middleware\NeedsTenant;
use App\Models\Tenant;
use App\Providers\TenancyServiceProvider;
use App\Services\Tenancy\DomainTenantFinder;
use App\Services\Tenancy\SwitchTenantFilesystemsTask;
use App\Services\Tenancy\SwitchTenantUrlTask;
use Filament\Facades\Filament;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\URL;
use Tests\InteractsWithTenants;
use Tests\TestCase;

class TenancyTest extends TestCase
{
    use InteractsWithTenants;
    use RefreshDatabase;

    public function test_a_single_tenant_install_registers_its_database_as_its_tenant(): void
    {
        $tenant = Tenant::sole();

        $this->assertSame(config('app.name'), $tenant->name);
        $this->assertSame(':memory:', $tenant->database);
        $this->assertNull($tenant->storage_folder);
        $this->assertSame([parse_url(config('app.url'), PHP_URL_HOST)], $tenant->domains->pluck('domain')->all());
        $this->assertTrue($tenant->isCurrent());

        (require database_path('migrations/landlord/2026_10_24_100100_register_the_single_tenant.php'))->up();
        $this->assertSame(1, Tenant::count());
    }

    public function test_a_single_tenant_install_answers_on_any_host(): void
    {
        $finder = app(DomainTenantFinder::class);

        $this->assertTrue(Tenant::sole()->is($finder->findForRequest(Request::create('https://anything.example/admin'))));
    }

    public function test_tenants_are_found_from_their_domains_in_a_multi_tenant_install(): void
    {
        config(['multitenancy.landlord_database' => 'landlord']);
        $first = Tenant::sole();
        $second = Tenant::create(['name' => 'Second school', 'database' => 'second']);
        $second->domains()->create(['domain' => 'Second.Example.org', 'is_primary' => true]);
        $finder = app(DomainTenantFinder::class);

        $this->assertTrue($second->is($finder->findForRequest(Request::create('https://second.example.org/'))));
        $this->assertNull($finder->findForRequest(Request::create('https://unknown.example.org/')));

        config(['multitenancy.default_tenant' => $first->id]);
        $this->assertTrue($first->is($finder->findForRequest(Request::create('https://unknown.example.org/'))));
    }

    public function test_requests_without_tenant_are_refused(): void
    {
        Tenant::forgetCurrent();

        $this->get('/')->assertNotFound();
        $this->get('/admin/login')->assertNotFound();
    }

    public function test_the_tenant_is_checked_before_the_session_starts(): void
    {
        // Without tenant, the session table of the default database may not exist
        $groups = [
            'panel' => Filament::getPanel('admin')->getMiddleware(),
            'web' => app(Router::class)->getMiddlewareGroups()['web'],
        ];

        foreach ($groups as $name => $middleware) {
            $this->assertLessThan(array_search(StartSession::class, $middleware), array_search(NeedsTenant::class, $middleware), $name);
        }
    }

    public function test_a_session_of_another_tenant_is_refused(): void
    {
        $this->get('/admin/login')->assertSuccessful();

        $this->withSession(['ensure_valid_tenant_session_tenant_id' => 999])
            ->get('/admin/login')
            ->assertUnauthorized();
    }

    public function test_a_single_tenant_install_keeps_its_app_url(): void
    {
        config(['app.url' => 'https://new-address.example.org']);
        $task = app(SwitchTenantUrlTask::class);

        $task->makeCurrent(Tenant::sole());

        $this->assertSame('https://new-address.example.org', config('app.url'));
        $this->assertSame('https://new-address.example.org/admin', url('/admin'));
    }

    public function test_url_and_files_follow_the_tenant(): void
    {
        config(['app.url' => 'https://academico.example', 'multitenancy.landlord_database' => 'landlord']);
        $tenant = Tenant::create(['name' => 'Second school', 'database' => 'second', 'storage_folder' => 'framework/testing/tenants/2']);
        $this->beforeApplicationDestroyed(fn () => File::deleteDirectory(storage_path('framework/testing/tenants')));
        $tenant->domains()->create(['domain' => 'www.second.example.org']);
        $tenant->domains()->create(['domain' => 'second.example.org', 'is_primary' => true]);
        $urlTask = app(SwitchTenantUrlTask::class);
        $filesystemsTask = app(SwitchTenantFilesystemsTask::class);

        $urlTask->makeCurrent($tenant);
        $filesystemsTask->makeCurrent($tenant);

        $this->assertSame('https://second.example.org', config('app.url'));
        $this->assertSame('https://second.example.org/admin', url('/admin'));
        $this->assertSame(storage_path('framework/testing/tenants/2/app/private/photo.jpg'), Storage::disk('local')->path('photo.jpg'));
        $this->assertSame(storage_path('framework/testing/tenants/2/app/public/logo.png'), Storage::disk('public')->path('logo.png'));
        $this->assertSame('https://second.example.org/storage/logo.png', Storage::disk('public')->url('logo.png'));

        $urlTask->forgetCurrent();
        $filesystemsTask->forgetCurrent();

        $this->assertSame('https://academico.example/admin', url('/admin'));
        $this->assertSame(storage_path('app/private/photo.jpg'), Storage::disk('local')->path('photo.jpg'));
    }

    public function test_each_tenant_reads_its_own_database_and_settings(): void
    {
        $this->useSeveralTenants();
        $first = $this->tenantWithSettings('first', ['academico.currency_symbol' => '$', 'academico.company_name' => 'First school']);
        $second = $this->tenantWithSettings('second', ['academico.currency_symbol' => '£']);
        $defaultName = config('app.name');

        $first->makeCurrent();
        $this->assertSame('$', config('academico.currency_symbol'));
        $this->assertSame('First school', config('app.name'));
        $this->assertSame(1, DB::table('settings')->where('value', json_encode('$'))->count());
        Cache::put('cached', 'first school');

        $second->makeCurrent();
        $this->assertSame('£', config('academico.currency_symbol'));
        $this->assertSame($defaultName, config('app.name'));
        $this->assertSame(0, DB::table('settings')->where('value', json_encode('$'))->count());
        $this->assertNull(Cache::get('cached'));

        $first->makeCurrent();
        $this->assertSame('$', config('academico.currency_symbol'));
    }

    public function test_signed_urls_of_a_tenant_are_refused_by_the_others(): void
    {
        $this->useSeveralTenants();
        $first = $this->tenantWithSettings('first');
        $second = $this->tenantWithSettings('second');

        $first->makeCurrent();
        $request = Request::create(Storage::disk('local')->temporaryUrl('1/photo.jpg', now()->addMinutes(5)));
        $this->assertTrue(URL::hasValidSignature($request, absolute: false));

        $second->makeCurrent();
        $this->assertFalse(URL::hasValidSignature($request, absolute: false));
    }

    public function test_a_single_tenant_install_keeps_its_configured_database(): void
    {
        Tenant::sole()->update(['database' => 'renamed_since']);
        Tenant::forgetCurrent();

        Tenant::sole()->makeCurrent();

        $this->assertSame(':memory:', config('database.connections.sqlite.database'));
        $this->assertSame(1, Tenant::count());
    }

    public function test_a_database_url_is_refused_in_a_multi_tenant_install(): void
    {
        config([
            'multitenancy.landlord_database' => 'landlord',
            'multitenancy.landlord_database_connection_name' => 'landlord_url_test',
            'database.connections.sqlite.url' => 'mysql://user:secret@db/academico',
        ]);

        $this->expectExceptionMessage('DB_URL cannot be used with LANDLORD_DB_DATABASE');

        (new TenancyServiceProvider($this->app))->register();
    }
}
