<?php

namespace App\Providers;

use App\Interfaces\InvoicingInterface;
use App\Interfaces\LMSInterface;
use App\Interfaces\MailingSystemInterface;
use App\Models\Book;
use App\Models\Config;
use App\Models\ContactRelationship;
use App\Models\EnrollmentStatusType;
use App\Models\Period;
use App\Models\Room;
use App\Models\Teacher;
use App\Services\CertificatesService;
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
                $periods = Period::where('id', '>=', $firstPeriod->id)->get();
            } else {
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

        View::composer('partials.add_book_to_student', function ($view) {
            $view->with('books', Book::all());
            $view->with('statuses', EnrollmentStatusType::all());
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


        $certificatesStyle = config('certificates-generation.style');

        if ($certificatesStyle) {
            $certificatesService = config("certificates-generation.{$certificatesStyle}.class");
            $this->app->bind(
                CertificatesService::class,
                $certificatesService
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
