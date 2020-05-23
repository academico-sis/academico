<?php

namespace App\Console;

use App\Mail\AdminReminders;
use App\Models\Attendance;
use App\Models\Period;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

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
            (new Attendance)->remindPendingAttendance();
        })->dailyAt('08:15');

        $schedule->call(function () {
            Log::info('Sending admin reminders');
            $changeNextPeriod = Carbon::parse(Period::get_enrollments_period()->end)->diffInDays() < 15;
            $changeCurrentPeriod = Carbon::parse(Period::get_default_period()->end) < Carbon::now();

            // if today is towards the end of the default enrollments period
            if ($changeNextPeriod || $changeCurrentPeriod) {
                Mail::to(config('settings.manager_email'))->queue(new AdminReminders($changeNextPeriod, $changeCurrentPeriod));
            }
        })->dailyAt('08:05');

        $schedule->command('monitor:check-uptime')->everyMinute();
        $schedule->command('activitylog:clean')->daily();
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
