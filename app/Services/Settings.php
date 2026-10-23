<?php

namespace App\Services;

use App\Models\Setting;

/**
 * Institution settings stored in the database.
 *
 * A setting named after one of the CONFIG_KEYS overrides that config value, so the rest of the
 * application keeps reading it with config(); the config files only hold the defaults.
 * Other settings, such as the current period, are read with get().
 */
class Settings
{
    /**
     * @var list<string>
     */
    public const CONFIG_KEYS = [
        'academico.company_name',
        'academico.company_address',
        'academico.company_phone',
        'academico.company_id',
        'academico.company_email',
        'academico.logo',
        'academico.calendar_start',
        'academico.currency_symbol',
        'academico.currency_code',
        'academico.currency_position',
        'academico.send_emails_for_absences',
        'academico.send_emails_for_results',
        'academico.include_takings_in_reports',
        'app.locale',
        'settings.manager_email',
        'settings.reports_email',
        'settings.secretary_email',
        'settings.courses_timezone',
        'settings.partnership_alerts',
        'settings.external_courses_enabled',
        'settings.external_courses_report',
        'settings.monthly_report',
        'settings.teachers_can_edit_result',
        'settings.attendance_reports_enabled',
        'settings.teachers_can_view_calendars',
        'settings.collect_student_banking_info',
        'settings.members_enabled',
        'registration.picture.enabled',
        'registration.picture.mandatory',
        'registration.ensure_email_unicity',
        'registration.terms_url',
        'registration.rules_url',
        'invoicing.accounting_enabled',
        'invoicing.allow_scheduled_payments',
        'invoicing.invoice_numbering',
        'invoicing.allow_empty_payment_methods',
        'invoicing.adding_scholarship_marks_as_paid',
        'invoicing.price_categories_enabled',
        'invoicing.skip_invoice_data_step',
        'invoicing.add_books_to_invoices',
        'invoicing.invoices_contain_enrollments_only',
        'invoicing.invoice_footer',
    ];

    /**
     * @var array<string, mixed>|null
     */
    private ?array $values = null;

    public function get(string $key, mixed $default = null): mixed
    {
        return $this->values()[$key] ?? $default;
    }

    /**
     * @param  array<string, mixed>  $values
     */
    public function set(array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::query()->updateOrCreate(['key' => $key], ['value' => $value]);
        }

        $this->apply();
    }

    /**
     * Reload the settings from the database and apply them to the config.
     * The institution name is also used as application name and mail sender name.
     */
    public function apply(): void
    {
        $this->values = Setting::query()->pluck('value', 'key')->all();

        $configValues = array_intersect_key($this->values, array_flip(self::CONFIG_KEYS));

        if (isset($configValues['academico.company_name'])) {
            $configValues['app.name'] = $configValues['academico.company_name'];
            $configValues['mail.from.name'] = $configValues['academico.company_name'];
        }

        config($configValues);

        if (isset($configValues['app.locale'])) {
            app()->setLocale($configValues['app.locale']);
        }

        if (isset($configValues['mail.from.name']) && app()->resolved('mail.manager')) {
            app('mail.manager')->purge();
        }
    }

    /**
     * @return array<string, mixed>
     */
    private function values(): array
    {
        if ($this->values === null) {
            $this->values = Setting::query()->pluck('value', 'key')->all();
        }

        return $this->values;
    }
}
