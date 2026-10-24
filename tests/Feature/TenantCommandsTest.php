<?php

namespace Tests\Feature;

use App\Events\MonthlyReportEvent;
use App\Models\Period;
use App\Models\Tenant;
use App\Models\User;
use Filament\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use RuntimeException;
use Tests\InteractsWithTenants;
use Tests\TestCase;

class TenantCommandsTest extends TestCase
{
    use InteractsWithTenants;
    use RefreshDatabase;

    /**
     * @return array<string, string>
     */
    private function newInstitution(string $domain = 'Second.Example.org'): array
    {
        return [
            'name' => 'Second School',
            'domain' => $domain,
            '--database' => $this->tenantDatabasePath('second'),
            '--admin-email' => 'jane@example.org',
            '--admin-firstname' => 'Jane',
            '--admin-lastname' => 'Doe',
        ];
    }

    public function test_institutions_cannot_be_created_in_a_single_tenant_install(): void
    {
        $this->artisan('academico:tenant:create', ['name' => 'Second School', 'domain' => 'second.example.org'])
            ->expectsOutputToContain('This install serves a single institution')
            ->assertFailed();

        $this->assertSame(1, Tenant::count());
    }

    public function test_an_institution_is_created_with_its_database_domain_and_administrator(): void
    {
        Notification::fake();
        $this->useSeveralTenants();

        $this->artisan('academico:tenant:create', $this->newInstitution())
            ->expectsOutputToContain('http://second.example.org/admin/password-reset/reset')
            ->assertSuccessful();

        $tenant = Tenant::sole();
        $this->assertSame('Second School', $tenant->name);
        $this->assertSame("tenants/{$tenant->id}", $tenant->storage_folder);
        $this->assertSame(['second.example.org'], $tenant->domains()->where('is_primary', true)->pluck('domain')->all());

        $tenant->makeCurrent();
        $admin = User::where('email', 'jane@example.org')->sole();
        $this->assertTrue($admin->hasRole('admin'));
        $this->assertSame('Second School', config('academico.company_name'));
        $this->assertSame(1, Period::count());
        Notification::assertSentTo($admin, ResetPassword::class);
    }

    public function test_the_database_is_named_after_the_prefix_and_the_institution(): void
    {
        Notification::fake();
        $this->useSeveralTenants();
        // SQLite databases are files: the prefix is a path
        $prefix = dirname($this->tenantDatabasePath('any')).'/school_';
        config(['multitenancy.database_prefix' => $prefix]);

        $this->artisan('academico:tenant:create', [...$this->newInstitution(), '--database' => null])->assertSuccessful();

        $this->assertSame("{$prefix}second_school", Tenant::sole()->database);
    }

    public function test_creation_refuses_a_domain_or_database_already_used(): void
    {
        Notification::fake();
        $this->useSeveralTenants();
        $this->artisan('academico:tenant:create', $this->newInstitution())->assertSuccessful();

        $this->artisan('academico:tenant:create', [...$this->newInstitution('second.example.org'), '--database' => $this->tenantDatabasePath('third')])
            ->expectsOutputToContain('domain has already been taken')
            ->assertFailed();
        $this->artisan('academico:tenant:create', $this->newInstitution('third.example.org'))
            ->expectsOutputToContain('database has already been taken')
            ->assertFailed();
        $this->artisan('academico:tenant:create', [...$this->newInstitution('not a domain'), '--database' => $this->tenantDatabasePath('third')])
            ->expectsOutputToContain('format is invalid')
            ->assertFailed();

        $this->assertSame(1, Tenant::count());
    }

    public function test_a_failed_creation_leaves_nothing_behind(): void
    {
        $this->useSeveralTenants();
        Notification::fake();
        User::creating(fn () => throw new RuntimeException('The administrator could not be saved'));

        try {
            $this->artisan('academico:tenant:create', $this->newInstitution());
            $this->fail('The creation should have failed.');
        } catch (RuntimeException $exception) {
            $this->assertSame('The administrator could not be saved', $exception->getMessage());
        }

        $this->assertSame(0, Tenant::count());
        $this->assertFileDoesNotExist($this->tenantDatabasePath('second'));
    }

    public function test_domains_can_be_added_made_primary_and_removed(): void
    {
        $this->useSeveralTenants();
        $tenant = $this->tenantWithSettings('school');

        $this->artisan('academico:tenant:domain', ['tenant' => 'school', 'domain' => 'school.example.org'])->assertSuccessful();
        $this->artisan('academico:tenant:domain', ['tenant' => $tenant->id, 'domain' => 'www.school.example.org'])->assertSuccessful();
        $this->assertSame('school.example.org', $tenant->domains()->where('is_primary', true)->value('domain'));

        $this->artisan('academico:tenant:domain', ['tenant' => $tenant->id, 'domain' => 'WWW.school.example.org', '--primary' => true])->assertSuccessful();
        $this->assertSame(['www.school.example.org'], $tenant->domains()->where('is_primary', true)->pluck('domain')->all());

        $this->artisan('academico:tenant:domain', ['tenant' => $tenant->id, 'domain' => 'school.example.org', '--remove' => true])->assertSuccessful();
        $this->assertSame(['www.school.example.org'], $tenant->domains()->pluck('domain')->all());

        $this->artisan('academico:tenant:domain', ['tenant' => 'unknown', 'domain' => 'other.example.org'])->assertFailed();

        $this->artisan('academico:tenant:list')
            ->expectsOutputToContain('www.school.example.org')
            ->assertSuccessful();
    }

    public function test_scheduled_commands_run_for_each_tenant(): void
    {
        Event::fake([MonthlyReportEvent::class]);
        $this->useSeveralTenants();
        $this->tenantWithSettings('first', ['settings.monthly_report' => true]);
        $this->tenantWithSettings('second', ['settings.monthly_report' => true]);
        $this->tenantWithSettings('third');

        $this->artisan('academico:monthly-report')->assertSuccessful();
        Event::assertDispatchedTimes(MonthlyReportEvent::class, 2);

        $this->artisan('academico:monthly-report', ['--tenant' => ['second']])->assertSuccessful();
        Event::assertDispatchedTimes(MonthlyReportEvent::class, 3);
    }

    public function test_a_failure_for_one_tenant_does_not_stop_the_others(): void
    {
        $this->useSeveralTenants();
        $this->tenantWithSettings('first');
        $this->tenantWithSettings('second');

        // These databases have no periods table
        $this->artisan('academico:check-periods')
            ->expectsOutputToContain('Tenant 1 (first)')
            ->expectsOutputToContain('Tenant 2 (second)')
            ->assertFailed();
    }

    public function test_migrations_run_for_the_landlord_and_every_institution(): void
    {
        Notification::fake();
        $this->useSeveralTenants();
        $this->artisan('academico:tenant:create', $this->newInstitution())->assertSuccessful();

        $this->artisan('academico:migrate')
            ->expectsOutputToContain('Landlord database')
            ->expectsOutputToContain('Second School')
            ->assertSuccessful();
    }

    public function test_migrations_of_a_single_tenant_install_run_as_usual(): void
    {
        $this->artisan('academico:migrate')->assertSuccessful();
    }

    public function test_failed_jobs_are_logged(): void
    {
        $uuid = (string) Str::uuid();

        app('queue.failer')->log('database', 'default', json_encode(['uuid' => $uuid]), new RuntimeException('Failed'));

        $this->assertTrue(DB::table('failed_jobs')->where('uuid', $uuid)->exists());
    }
}
