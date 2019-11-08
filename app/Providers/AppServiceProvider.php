<?php

namespace App\Providers;

use App\Models\Room;
use App\Models\User;
use App\Models\Period;

use App\Models\Teacher;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\Schema;
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
        
        if (\Schema::hasTable('periods')) {
            $periods = Period::orderBy('id','desc')->get();
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
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        if ($this->app->isLocal()) {
            $this->app->register(TelescopeServiceProvider::class);
        }
    }
}
