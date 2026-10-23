<?php

namespace Tests\Feature;

use App\Filament\Pages\Settings\GeneralSettings;
use App\Models\Period;
use App\Models\User;
use App\Services\Settings;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;
use Tests\TestCase;

class GeneralSettingsPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        Role::findOrCreate('admin', 'web');
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $admin = User::factory()->create();
        $admin->assignRole('admin');
        $this->actingAs($admin);
    }

    public function test_admin_can_open_the_page(): void
    {
        $this->get(GeneralSettings::getUrl())->assertSuccessful();
    }

    public function test_other_users_cannot_open_the_page(): void
    {
        $this->actingAs(User::factory()->create());

        $this->get(GeneralSettings::getUrl())->assertForbidden();
    }

    public function test_form_shows_the_current_values(): void
    {
        app(Settings::class)->set([
            'academico.company_name' => 'Example School',
            'invoicing.accounting_enabled' => true,
        ]);

        Livewire::test(GeneralSettings::class)
            ->assertFormSet([
                'academico.company_name' => 'Example School',
                'academico.currency_symbol' => '€',
                'invoicing.accounting_enabled' => true,
                'registration.picture.enabled' => true,
            ]);
    }

    public function test_saving_stores_and_applies_the_settings(): void
    {
        $period = Period::factory()->create();

        Livewire::test(GeneralSettings::class)
            ->fillForm([
                'academico.company_name' => 'Example School',
                'academico.currency_symbol' => '$',
                'academico.currency_position' => 'before',
                'settings.courses_timezone' => 'America/Bogota',
                'settings.members_enabled' => true,
                'registration.picture.mandatory' => true,
                'invoicing.invoice_numbering' => 'manual',
                'invoicing.invoice_footer' => 'Thank you',
                'first_period' => $period->id,
            ])
            ->call('save')
            ->assertHasNoFormErrors()
            ->assertNotified(__('Settings saved'));

        $this->assertSame('Example School', config('academico.company_name'));
        $this->assertSame('$', config('academico.currency_symbol'));
        $this->assertSame('before', config('academico.currency_position'));
        $this->assertSame('America/Bogota', config('settings.courses_timezone'));
        $this->assertTrue(config('settings.members_enabled'));
        $this->assertTrue(config('registration.picture.mandatory'));
        $this->assertSame('manual', config('invoicing.invoice_numbering'));
        $this->assertSame('Thank you', config('invoicing.invoice_footer'));
        $this->assertSame($period->id, app(Settings::class)->get('first_period'));
        $this->assertDatabaseHas('settings', ['key' => 'academico.company_name']);
    }

    public function test_deleted_period_does_not_block_saving(): void
    {
        app(Settings::class)->set(['first_period' => 99999]);

        Livewire::test(GeneralSettings::class)
            ->assertFormSet(['first_period' => null])
            ->call('save')
            ->assertHasNoFormErrors();
    }

    public function test_empty_fields_are_saved_as_null(): void
    {
        Livewire::test(GeneralSettings::class)
            ->fillForm(['settings.manager_email' => ''])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNull(config('settings.manager_email'));
    }

    public function test_institution_name_is_required(): void
    {
        Livewire::test(GeneralSettings::class)
            ->fillForm(['academico.company_name' => ''])
            ->call('save')
            ->assertHasFormErrors(['academico.company_name' => 'required']);
    }

    public function test_logo_is_stored_on_the_public_disk(): void
    {
        Storage::fake('public');

        Livewire::test(GeneralSettings::class)
            ->fillForm(['academico.logo' => UploadedFile::fake()->image('logo.png')])
            ->call('save')
            ->assertHasNoFormErrors();

        $this->assertNotNull(config('academico.logo'));
        Storage::disk('public')->assertExists(config('academico.logo'));
    }

    public function test_panel_shows_the_institution_logo(): void
    {
        app(Settings::class)->set(['academico.logo' => 'settings/logo.png']);

        $this->get(GeneralSettings::getUrl())
            ->assertSee(Storage::disk('public')->url('settings/logo.png'));
    }

    public function test_student_pages_show_the_institution_name(): void
    {
        app(Settings::class)->set(['academico.company_name' => 'Example School']);
        auth()->logout();

        $this->get(route('register'))
            ->assertSuccessful()
            ->assertSee('Example School');
    }
}
