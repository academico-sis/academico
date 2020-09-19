<?php

namespace App\Providers;

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
            $periods = Period::orderBy('id', 'desc')->get();
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
        //
    }
}
