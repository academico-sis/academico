<?php

namespace App\Providers;

use App\Services\CourseEventScheduler;
use App\Services\Settings;
use Filament\Tables\Enums\RecordActionsPosition;
use Filament\Tables\Table;
use Illuminate\Database\QueryException;
use Illuminate\Queue\Events\JobProcessing;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // holds the moment from which course changes apply to the sessions, for the current request
        $this->app->scoped(CourseEventScheduler::class);

        $this->app->singleton(Settings::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->applySettings();

        $this->registerGates();

        Table::configureUsing(function (Table $table): Table {
            return $table->recordActionsPosition(RecordActionsPosition::BeforeCells);
        });
    }

    /**
     * Apply the settings saved in the database once the application has booted, and again before
     * each queued job so that long-running workers see the latest values. The settings table does
     * not exist before the first migration, in which case the config defaults are used.
     *
     * The institution language becomes the default locale. Web requests then switch to the
     * language of the user (see SetLocale).
     */
    protected function applySettings(): void
    {
        $this->app->booted(function (): void {
            try {
                $this->app->make(Settings::class)->apply();
            } catch (QueryException) {
                // Use the config defaults.
            }

            $this->app->setLocale(config('academico.locale'));
        });

        Queue::before(function (JobProcessing $event): void {
            $this->app->make(Settings::class)->apply();

            // A sync job runs inside the request and keeps the language of its user.
            if ($event->connectionName !== 'sync') {
                $this->app->setLocale(config('academico.locale'));
            }
        });
    }

    protected function registerGates(): void
    {
        // A user can edit course grades if they are the course teacher,
        // or if they have explicit permission
        Gate::define('edit-course-grades', fn ($user, $course) => ($user->isTeacher() && $user->id == $course->teacher_id) || $user->can('evaluation.edit'));

        // A user can view course grades and evaluations if they are the course teacher,
        // or if they have explicit permission
        Gate::define('view-course-grades', fn ($user, $course) => ($user->isTeacher() && $user->id == $course->teacher_id) || $user->can('evaluation.view') || $user->can('evaluation.edit'));

        // A user can view a course attendance sheet if they are the course teacher,
        // or if they have explicit permission
        Gate::define('view-course-attendance', fn ($user, $course) => ($user->isTeacher() && $user->id == $course->teacher_id) || $user->can('attendance.view'));

        // A user can view an event attendance sheet if they are the event teacher,
        // the course teacher, or if they have explicit permission
        Gate::define('view-event-attendance', fn ($user, $event) => ($event->teacher_id == $user->id) || ($event->course->teacher_id == $user->id) || $user->can('attendance.view'));

        // A user can edit attendance if they are the event teacher,
        // the course teacher, or if they have explicit permission
        Gate::define('edit-attendance', fn ($user, $event) => ($event->teacher_id == $user->id) || ($event->course->teacher_id == $user->id) || $user->can('attendance.edit'));

        // Teachers can view their own calendar,
        // users with explicit permission can view all calendars
        Gate::define('view-teacher-calendar', fn ($user, $teacher) => ($user->isTeacher() && $user->id == $teacher->id) || $user->can('calendars.view'));

        Gate::define('view-room-calendar', fn ($user) => ($user->isTeacher() && config('settings.teachers_can_view_calendars')) || $user->can('calendars.view'));

        // Teachers can view their own courses,
        // users with explicit permission can view all courses
        Gate::define('view-course', fn ($user, $course) => ($user->isTeacher() && $user->id === $course->teacher_id) || $user->can('courses.view'));

        // A user can view an enrollment if they are the student,
        // if they are the course teacher, or if they have explicit permission
        Gate::define('view-enrollment', fn ($user, $enrollment) => ($user->isStudent() && $user->id == $enrollment->student_id) || ($user->isTeacher() && $user->id == $enrollment->course?->teacher_id) || $user->can('evaluation.view'));

        // The course teacher or users with enrollment edit permission can enroll in a course
        Gate::define('enroll-in-course', fn ($user, $course) => $course->teacher_id == $user->id || $user->can('enrollments.edit'));

        // Teachers or users with enrollment edit permission can enroll students
        Gate::define('enroll-students', fn ($user) => $user->isTeacher() || $user->can('enrollments.edit'));

        // Teachers can view their own hours,
        // users with explicit permission can view all hours
        Gate::define('view-teacher-hours', fn ($user, $teacher) => ($user->isTeacher() && $user->id == $teacher->id) || $user->can('hr.view'));

        // Teachers can edit results for their own students (if config allows),
        // users with explicit permission can edit any result
        Gate::define('edit-result', function ($user, $enrollment) {
            if ($user->can('evaluation.edit')) {
                return true;
            }

            if (config('settings.teachers_can_edit_result')) {
                return $user->isTeacher() && $user->id === $enrollment->course->teacher_id;
            }

            return false;
        });
    }
}
