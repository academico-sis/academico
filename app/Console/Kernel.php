<?php

namespace App\Console;

use App\Models\Attendance;
use App\Models\Config;
use App\Models\Period;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            Log::info('Sending attendance reminders');
            (new Attendance())->remindPendingAttendance();
        })->dailyAt('08:15');

        $schedule->call(function () {
            Log::info('Checking default periods');

            // when we finish the current period; remove the manual override to automatically fallbackto the next one
            $changeCurrentPeriod = Carbon::parse(Period::get_default_period()->end) < Carbon::now();
            if ($changeCurrentPeriod) {
                Log::info('Removing manual current period override');
                Config::where('name', 'current_period')->update(['value' => null]);
            }

            // if the enrollment period is the same as the current period, we can remove it
            if (Period::get_enrollments_period() == Period::get_default_period()) {
                Log::info('Removing manual enrollment period override');
                Config::where('name', 'default_enrollment_period')->update(['value' => null]);
            }
        })->dailyAt('00:00');

        $schedule->command('activitylog:clean')->daily();

        $schedule->command('telescope:prune --hours=96')->daily();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
