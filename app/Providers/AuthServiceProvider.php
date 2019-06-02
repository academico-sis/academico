<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        /**
         * a user is allowed to view the course attendance sheet if they are the teacher for this course,
         * or if they have explicit permission to view all course attendance sheets
         */
        Gate::define('view-course-attendance', function ($user, $course) {
            return ($user->teacher_id == $course->teacher_id) || $user->can('attendance.view');
        });


        /**
         * a user is allowed to view the event attendance sheet if they are the teacher for this event,
         * if they are the teacher for this course,
         * or if they have explicit permission to view all event attendance sheets
         */
        Gate::define('view-event-attendance', function ($user, $event) {
            return $event->teacher_id == $user->teacher_id || $event->course->teacher_id == $user->teacher_id || $user->can('attendance.view');
        });


        /**
         * a user is allowed to edit an attendance sheet if they are the teacher for the event,
         * if they are the teacher for the course,
         * or if they have explicit permission to edit any attendance sheets
         */
        Gate::define('edit-attendance', function ($user, $event) {
            return $event->teacher_id == $user->teacher_id || $event->course->teacher_id == $user->teacher_id || $user->can('attendance.edit');
        });


        /**
         * teachers are allowed to view their own calendar,
         * and users with explicit permission can view all calendars
         */
        Gate::define('view-teacher-calendar', function ($user, $teacher) {
            return $user->teacher_id == $teacher->id || $user->can('calendars.view');
        });
        
        
        /**
         * teachers are allowed to view their own courses,
         * and users with explicit permission can view all courses
         */
        Gate::define('view-course', function ($user, $course) {
            return $user->teacher_id == $course->teacher_id || $user->can('courses.view');
        });


        /**
         * the user is allowed to view the result if they are the student,
         * if they are the teacher of the course for this result
         * of if they have explicit permission to view any result
         */
        Gate::define('view-enrollment', function ($user, $enrollment) {
            return $user->id == $enrollment->student_id || $user->teacher_id == $enrollment->course->teacher_id || $user->can('evaluation.view');
        });

        /**
         * if the user is the teacher of the course
         * of if they have explicit permission to enroll students
         */
        Gate::define('enroll-in-course', function ($user, $course) {
            return ($course->teacher_id == $user->teacher_id) || $user->can('enrollments.create');
        });
        

        /**
         * teachers are allowed to view their own hours,
         * and users with explicit permission can view all hours
         */
        Gate::define('view-teacher-hours', function ($user, $teacher) {
            return $user->teacher_id == $teacher->id || $user->can('hr.view');
        });

    }
}
