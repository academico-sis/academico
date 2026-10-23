<?php

namespace Tests\Feature;

use App\Models\Setting;
use App\Services\Settings;
use Illuminate\Contracts\Queue\Job;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Mail\Message;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class SettingsServiceTest extends TestCase
{
    use RefreshDatabase;

    public function test_get_returns_the_default_without_saved_value(): void
    {
        $this->assertSame('fallback', app(Settings::class)->get('unknown', 'fallback'));
    }

    public function test_set_saves_values(): void
    {
        app(Settings::class)->set(['current_period' => 3, 'invoicing.invoice_footer' => 'Thank you']);

        $this->assertSame(3, app(Settings::class)->get('current_period'));
        $this->assertDatabaseHas('settings', ['key' => 'invoicing.invoice_footer']);
        $this->assertSame('Thank you', Setting::firstWhere('key', 'invoicing.invoice_footer')->value);
    }

    public function test_config_keys_override_the_config(): void
    {
        app(Settings::class)->set([
            'academico.currency_symbol' => '$',
            'invoicing.accounting_enabled' => true,
            'registration.picture.mandatory' => true,
        ]);

        $this->assertSame('$', config('academico.currency_symbol'));
        $this->assertTrue(config('invoicing.accounting_enabled'));
        $this->assertTrue(config('registration.picture.mandatory'));
    }

    public function test_config_keys_keep_their_default_without_saved_value(): void
    {
        app(Settings::class)->apply();

        $this->assertSame('€', config('academico.currency_symbol'));
        $this->assertFalse(config('invoicing.accounting_enabled'));
    }

    public function test_other_keys_do_not_change_the_config(): void
    {
        $defaultConnection = config('database.default');

        app(Settings::class)->set(['database.default' => 'other', 'current_period' => 3]);

        $this->assertSame($defaultConnection, config('database.default'));
        $this->assertNull(config('current_period'));
    }

    public function test_institution_name_is_used_as_application_and_sender_name(): void
    {
        app(Settings::class)->set(['academico.company_name' => 'Example School']);

        $this->assertSame('Example School', config('app.name'));
        $this->assertSame('Example School', config('mail.from.name'));
    }

    public function test_mails_use_the_new_sender_name_after_a_change(): void
    {
        Mail::mailer('array');

        app(Settings::class)->set(['academico.company_name' => 'Example School']);

        Mail::raw('Hello', fn (Message $message) => $message->to('student@example.com'));

        $sentMessage = Mail::mailer('array')->getSymfonyTransport()->messages()->last();
        $this->assertSame('Example School', $sentMessage->getOriginalMessage()->getFrom()[0]->getName());
    }

    public function test_saving_settings_keeps_the_language_of_the_current_user(): void
    {
        app()->setLocale('es');

        app(Settings::class)->set(['academico.locale' => 'fr']);

        $this->assertSame('fr', config('academico.locale'));
        $this->assertSame('es', app()->getLocale());
    }

    public function test_queue_workers_use_the_institution_language(): void
    {
        app(Settings::class)->set(['academico.locale' => 'fr']);
        app()->setLocale('en');

        event(new JobProcessing('database', $this->createStub(Job::class)));

        $this->assertSame('fr', app()->getLocale());
    }

    public function test_sync_jobs_keep_the_language_of_the_request(): void
    {
        app(Settings::class)->set(['academico.locale' => 'fr']);
        app()->setLocale('es');

        dispatch(fn () => Cache::put('locale_in_job', app()->getLocale()));

        $this->assertSame('es', Cache::get('locale_in_job'));
    }

    public function test_apply_reloads_values_saved_by_another_process(): void
    {
        app(Settings::class)->set(['academico.currency_symbol' => '$']);

        Setting::where('key', 'academico.currency_symbol')->update(['value' => json_encode('£')]);
        app(Settings::class)->apply();

        $this->assertSame('£', config('academico.currency_symbol'));
    }

    public function test_queued_jobs_see_the_latest_settings(): void
    {
        app(Settings::class)->set(['academico.currency_symbol' => '$']);
        Setting::where('key', 'academico.currency_symbol')->update(['value' => json_encode('£')]);

        dispatch(fn () => Cache::put('currency_symbol_in_job', config('academico.currency_symbol')));

        $this->assertSame('£', Cache::get('currency_symbol_in_job'));
    }
}
