<?php

namespace App\Providers;

use App\Interfaces\InvoicingInterface;
use App\Interfaces\LMSInterface;
use App\Interfaces\MailingSystemInterface;
use App\Models\Config;
use App\Models\ContactRelationship;
use App\Models\Period;
use App\Models\Room;
use App\Models\Teacher;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Schema::defaultStringLength(191);

        if (\Schema::hasTable('periods') && \Schema::hasTable('config')) {
            $firstPeriod = Period::find(Config::where('name', 'first_period')->first()->value);

            if ($firstPeriod) {
                $periods = Period::where('id', '>=', $firstPeriod->id);
            }
            else {
                $periods = Period::all();
            }

            $current_period = Period::get_default_period();
            View::share('periods', $periods);
            View::share('current_period', $current_period);
        }

        if (\Schema::hasTable('teachers')) {
            View::share('teachers', Teacher::all());
        }

        if (\Schema::hasTable('rooms')) {
            View::share('rooms', Room::all());
        }

        View::composer(
            ['partials.create_new_contact', 'students.edit-contact'], function ($view) {
                $view->with('contact_types', ContactRelationship::all());
            });
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $syncTo = config('lms.sync_to');

        if ($syncTo) {
            $lms = config("lms.{$syncTo}.class");

            $this->app->bind(
                LMSInterface::class,
                $lms
            );
        }

        $invoicingSystem = config('invoicing.invoicing_system');

        if ($invoicingSystem) {
            $invoicingService = config("invoicing.{$invoicingSystem}.class");

            $this->app->bind(
                InvoicingInterface::class,
                $invoicingService
            );
        }

        $mailngSystem = config('mailing-system.mailing_system');

        if ($mailngSystem) {
            $mailingService = config("mailing-system.{$mailngSystem}.class");

            $this->app->bind(
                MailingSystemInterface::class,
                $mailingService
            );
        }
    }
}
