<?php

namespace App\Providers;

use App\Models\Period;
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

        \Blade::directive('lang_u', function ($s) {
            
            return "<?php echo mb_convert_case(trans($s), MB_CASE_TITLE, 'UTF-8'); ?>";
        });
        
        if (\Schema::hasTable('periods')) {
            $periods = Period::orderBy('id','desc')->get();
            $current_period = Period::get_default_period();
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
