<?php

namespace App\Providers;

use App\Models\Course;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\\Model' => 'App\\Policies\\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        /*
         * a user is allowed to edit the course grades if they are the teacher for this course,
         * or if they have explicit permission to do so
         */
        Gate::define('edit-course-grades', fn ($user, $course) => $user->isTeacher() && $user->id == $course->teacher_id || $user->can('evaluation.edit'));

        /*
         * a user is allowed to view the course attendance sheet if they are the teacher for this course,
         * or if they have explicit permission to view all course attendance sheets
         */
        Gate::define('view-course-attendance', fn ($user, $course) => $user->isTeacher() && $user->id == $course->teacher_id || $user->can('attendance.view'));

        /*
         * a user is allowed to view the event attendance sheet if they are the teacher for this event,
         * if they are the teacher for this course,
         * or if they have explicit permission to view all event attendance sheets
         */
        Gate::define('view-event-attendance', fn ($user, $event) => ($event->teacher_id == $user->id) || ($event->course->teacher_id == $user->id) || $user->can('attendance.view'));

        /*
         * a user is allowed to edit an attendance sheet if they are the teacher for the event,
         * if they are the teacher for the course,
         * or if they have explicit permission to edit any attendance sheets
         */
        Gate::define('edit-attendance', fn ($user, $event) => ($event->teacher_id == $user->id) || ($event->course->teacher_id == $user->id) || $user->can('attendance.edit'));

        /*
         * teachers are allowed to view their own calendar,
         * and users with explicit permission can view all calendars
         */
        Gate::define('view-teacher-calendar', fn ($user, $teacher) => ($user->isTeacher() && $user->id == $teacher->id) || $user->can('calendars.view'));

        Gate::define('view-room-calendar', fn ($user) => ($user->isTeacher() && config('settings.teachers_can_view_calendars')) || $user->can('calendars.view'));

        /*
         * teachers are allowed to view their own courses,
         * and users with explicit permission can view all courses
         */
        Gate::define('view-course', fn ($user, Course $course) => ($user->isTeacher() && $user->id === $course->teacher_id) || $user->can('courses.view'));

        /*
         * the user is allowed to view the result if they are the student,
         * if they are a teacher
         * of if they have explicit permission to view any result
         */
        Gate::define('view-enrollment', fn ($user, $enrollment) => ($user->isStudent() && $user->id == $enrollment->student_id) || $user->isTeacher() || $user->can('evaluation.view'));

        /*
         * if the user is the teacher of the course
         * of if they have explicit permission to enroll students
         */
        Gate::define('enroll-in-course', fn ($user, $course) => $course->teacher_id == $user->id || $user->can('enrollments.edit'));

        /*
         * if the user is a teacher
         * of if they have explicit permission to enroll students
         */
        Gate::define('enroll-students', fn ($user) => $user->isTeacher() || $user->can('enrollments.edit'));

        /*
         * teachers are allowed to view their own hours,
         * and users with explicit permission can view all hours
         */
        Gate::define('view-teacher-hours', fn ($user, $teacher) => ($user->isTeacher() && $user->id == $teacher->id) || $user->can('hr.view'));

        /*
         * teachers are allowed to edit results for their own students
         * as well as users with explicit permission to edit any result
         */
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
