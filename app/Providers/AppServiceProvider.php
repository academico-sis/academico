<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {

        \Blade::directive('lang_u', function ($s) {
            return "<?php echo ucfirst(trans($s)); ?>";
        });
        
        if (\Schema::hasTable('periods')) {
            $periods = \App\Models\Period::orderBy('id','desc')->get();
            $current_period = \App\Models\Period::get_default_period();
            View::share('periods', $periods);
            View::share('current_period', $current_period);

        }
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        
    }
}
