<?php

use App\Events\ExpiringPartnershipsEvent;
use App\Events\ExternalCoursesReportEvent;
use App\Events\MonthlyReportEvent;
use App\Models\Config;
use App\Models\Partner;
use App\Models\Period;
use App\Traits\HandlesAttendance;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schedule;

// Daily attendance reminders at 08:15
Schedule::call(function (): void {
    try {
        Log::info('Sending attendance reminders');
        $handler = new class
        {
            use HandlesAttendance;
        };
        $handler->remindPendingAttendance();
    } catch (Throwable $e) {
        Log::error('[Schedule::attendanceReminders] '.$e->getMessage(), ['exception' => $e::class]);
        if (function_exists('\Sentry\captureException')) {
            \Sentry\captureException($e);
        }
    }
})->dailyAt('08:15');

// Daily period check at midnight
Schedule::call(function (): void {
    try {
        Log::info('Checking default periods');
        $changeCurrentPeriod = Carbon::parse(Period::get_default_period()->end) < Carbon::now();

        if ($changeCurrentPeriod) {
            Config::where('name', 'current_period')->update(['value' => null]);
        }

        if (Period::get_enrollments_period() == Period::get_default_period()) {
            Config::where('name', 'default_enrollment_period')->update(['value' => null]);
        }
    } catch (Throwable $e) {
        Log::error('[Schedule::periodCheck] '.$e->getMessage(), ['exception' => $e::class]);
        if (function_exists('\Sentry\captureException')) {
            \Sentry\captureException($e);
        }
    }
})->dailyAt('00:00');

// Partnership expiry alerts at 02:05 (config-gated)
if (config('settings.partnership_alerts')) {
    Schedule::call(function (): void {
        try {
            $partners = Partner::where(function ($q): void {
                $q->whereNotNull('expired_on')->where('expired_on', '<', Carbon::now()->addDays(28));
            })->where(function ($q): void {
                $q->whereNull('last_alert_sent_at')
                    ->orWhere('last_alert_sent_at', '>', Carbon::now()->subDays(28));
            });

            if ($partners->count() > 0) {
                event(new ExpiringPartnershipsEvent($partners->get()));
            }
        } catch (Throwable $e) {
            Log::error('[Schedule::partnershipAlerts] '.$e->getMessage(), ['exception' => $e::class]);
            if (function_exists('\Sentry\captureException')) {
                \Sentry\captureException($e);
            }
        }
    })->dailyAt('02:05');
}

// External courses report at 02:10 (config-gated)
if (config('settings.external_courses_report')) {
    Schedule::call(function (): void {
        try {
            event(new ExternalCoursesReportEvent);
        } catch (Throwable $e) {
            Log::error('[Schedule::externalCoursesReport] '.$e->getMessage(), ['exception' => $e::class]);
            if (function_exists('\Sentry\captureException')) {
                \Sentry\captureException($e);
            }
        }
    })->dailyAt('02:10');
}

// Monthly report on the 20th (config-gated)
if (config('settings.monthly_report')) {
    Schedule::call(function (): void {
        try {
            event(new MonthlyReportEvent);
        } catch (Throwable $e) {
            Log::error('[Schedule::monthlyReport] '.$e->getMessage(), ['exception' => $e::class]);
            if (function_exists('\Sentry\captureException')) {
                \Sentry\captureException($e);
            }
        }
    })->monthlyOn(20);
}

// Clean activity log monthly
Schedule::command('activitylog:clean')->monthly();

// Build cached report daily at 05:15
Schedule::command('academico:build-report')->dailyAt('05:15');
