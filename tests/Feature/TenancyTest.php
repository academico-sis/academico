<?php

namespace Tests\Feature;

use App\Http\Middleware\NeedsTenant;
use App\Models\Tenant;
use App\Services\Tenancy\DomainTenantFinder;
use App\Services\Tenancy\SwitchTenantFilesystemsTask;
use App\Services\Tenancy\SwitchTenantUrlTask;
use Filament\Facades\Filament;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Routing\Router;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class TenancyTest extends TestCase
{
    use RefreshDatabase;

    private string $databasesFolder;

    protected function tearDown(): void
    {
        if (isset($this->databasesFolder)) {
            // Back to the test database, so that its transaction is rolled back
            config(['database.default' => 'sqlite']);
            File::deleteDirectory($this->databasesFolder);
        }

        parent::tearDown();
    }

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

    public function test_url_and_files_follow_the_tenant(): void
    {
        config(['app.url' => 'https://academico.example']);
        $tenant = Tenant::create(['name' => 'Second school', 'database' => 'second', 'storage_folder' => 'tenants/2']);
        $tenant->domains()->create(['domain' => 'www.second.example.org']);
        $tenant->domains()->create(['domain' => 'second.example.org', 'is_primary' => true]);
        $urlTask = app(SwitchTenantUrlTask::class);
        $filesystemsTask = app(SwitchTenantFilesystemsTask::class);

        $urlTask->makeCurrent($tenant);
        $filesystemsTask->makeCurrent($tenant);

        $this->assertSame('https://second.example.org', config('app.url'));
        $this->assertSame('https://second.example.org/admin', url('/admin'));
        $this->assertSame(storage_path('tenants/2/app/private/photo.jpg'), Storage::disk('local')->path('photo.jpg'));
        $this->assertSame(storage_path('tenants/2/app/public/logo.png'), Storage::disk('public')->path('logo.png'));
        $this->assertSame('https://second.example.org/storage/logo.png', Storage::disk('public')->url('logo.png'));

        $urlTask->forgetCurrent();
        $filesystemsTask->forgetCurrent();

        $this->assertSame('https://academico.example/admin', url('/admin'));
        $this->assertSame(storage_path('app/private/photo.jpg'), Storage::disk('local')->path('photo.jpg'));
    }

    public function test_each_tenant_reads_its_own_database_and_settings(): void
    {
        $first = $this->tenantWithDatabase('first', ['academico.currency_symbol' => '$', 'academico.company_name' => 'First school']);
        $second = $this->tenantWithDatabase('second', ['academico.currency_symbol' => '£']);
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

    /**
     * A tenant whose database is a SQLite file holding a settings table. The tenants are switched on a
     * separate default connection, which leaves the test database and its transaction alone, and the
     * landlord tables move to another in-memory database.
     *
     * @param  array<string, mixed>  $settings
     */
    private function tenantWithDatabase(string $name, array $settings): Tenant
    {
        if (! isset($this->databasesFolder)) {
            $this->databasesFolder = storage_path('framework/testing/tenancy');
            File::ensureDirectoryExists($this->databasesFolder);
            Tenant::forgetCurrent();

            config([
                'multitenancy.landlord_database' => 'landlord',
                'multitenancy.landlord_database_connection_name' => 'landlord_test',
                'database.connections.landlord_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
                'database.connections.tenant_test' => ['driver' => 'sqlite', 'database' => ':memory:', 'prefix' => ''],
                'database.default' => 'tenant_test',
            ]);
            Artisan::call('migrate', ['--database' => 'landlord_test', '--path' => 'database/migrations/landlord/2026_10_24_100000_create_tenants_tables.php']);
        }

        $database = "{$this->databasesFolder}/{$name}.sqlite";
        File::put($database, '');
        config(["database.connections.{$name}" => ['driver' => 'sqlite', 'database' => $database, 'prefix' => '']]);

        Schema::connection($name)->create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->json('value')->nullable();
            $table->timestamps();
        });
        foreach ($settings as $key => $value) {
            DB::connection($name)->table('settings')->insert(['key' => $key, 'value' => json_encode($value)]);
        }

        return Tenant::create(['name' => $name, 'database' => $database]);
    }
}
