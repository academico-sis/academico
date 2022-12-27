<?php

namespace App\Console;

use App\Events\ExpiringPartnershipsEvent;
use App\Events\ExternalCoursesReportEvent;
use App\Events\MonthlyReportEvent;
use App\Models\Config;
use App\Models\Partner;
use App\Models\Period;
use App\Traits\HandlesAttendance;
use Carbon\Carbon;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;
use Illuminate\Support\Facades\Log;

class Kernel extends ConsoleKernel
{
    use HandlesAttendance;

    /**
     * Define the application's command schedule.
     *
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            Log::info('Sending attendance reminders');
            $this->remindPendingAttendance();
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

        if (config('settings.partnership_alerts')) {
            $schedule->call(function () {
                Log::info('Checking expired partnerships');

                // if one of the partnerships is expiring soon, send an email
                $partners = Partner::where(function ($query) {
                    $query->whereNotNull('expired_on')->where('expired_on', '<', Carbon::now()->addDays(28));
                })
                    ->where(function ($query) {
                        $query->whereNull('last_alert_sent_at')->orWhere('last_alert_sent_at', '>', Carbon::now()->subDays(28))->get();
                    });

                if ($partners->count() > 0) {
                    event(new ExpiringPartnershipsEvent($partners->get()));
                }
            })->dailyAt('02:05');
        }

        if (config('settings.external_courses_report')) {
            $schedule->call(function () {
                Log::info('Sending external courses reports');
                event(new ExternalCoursesReportEvent());
            })->dailyAt('02:10');
        }

        if (config('settings.monthly_report')) {
            $schedule->call(function () {
                Log::info('Sending monthly hours report');
                event(new MonthlyReportEvent());
            })->monthlyOn(20);
        }

        $schedule->command('activitylog:clean')->monthly();

        $schedule->command('telescope:prune --hours=96')->daily();

        $schedule->command('academico:build-report')->dailyAt('05:15');
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
