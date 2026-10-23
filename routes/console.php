<?php

use Illuminate\Support\Facades\Schedule;

/*
 * These tasks run for each tenant, at a time of day in the institution timezone. The scheduler starts
 * them every 15 minutes, and each command only works for the tenants whose local time matches --at
 * (and day of the month matches --day). Commands that depend on a setting check it when they run.
 */
Schedule::command('academico:check-periods --at=00:00')->everyFifteenMinutes();
Schedule::command('academico:monthly-report --day=20 --at=00:00')->everyFifteenMinutes();
Schedule::command('academico:external-courses-report --at=03:15')->everyFifteenMinutes();
Schedule::command('academico:build-report --at=05:15')->everyFifteenMinutes();
Schedule::command('academico:attendance-reminders --at=08:15')->everyFifteenMinutes();

Schedule::command('tenants:artisan activitylog:clean')->monthly();
