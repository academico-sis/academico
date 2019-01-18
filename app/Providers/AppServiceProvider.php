<?php

namespace App\Providers;

use App\Models\Period;
use App\Models\User;
use App\Models\Room;

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

        \Blade::directive('lang', function ($s) {
            
            return "<?php echo mb_convert_case(trans($s), MB_CASE_TITLE, 'UTF-8'); ?>";
        });
        
        if (\Schema::hasTable('periods')) {
            $periods = Period::orderBy('id','desc')->get();
            $current_period = Period::get_default_period();
            View::share('periods', $periods);
            View::share('current_period', $current_period);
        }

        if (\Schema::hasTable('users')) {
            View::share('teachers', User::teacher()->all());
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
        
    }
}
