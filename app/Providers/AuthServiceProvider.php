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
            return ($user->id == $course->teacher_id) || $user->can('attendance.view');
        });


        /**
         * a user is allowed to view the event attendance sheet if they are the teacher for this event,
         * if they are the teacher for this course,
         * or if they have explicit permission to view all event attendance sheets
         */
        Gate::define('view-event-attendance', function ($user, $event) {
            return $event->teacher_id == $user->id || $event->course->teacher_id == $user->id || $user->can('attendance.view');
        });


        /**
         * a user is allowed to edit an attendance sheet if they are the teacher for the event,
         * if they are the teacher for the course,
         * or if they have explicit permission to edit any attendance sheets
         */
        Gate::define('edit-attendance', function ($user, $event) {
            return $event->teacher_id == $user->id || $event->course->teacher_id == $user->id || $user->can('attendance.edit');
        });
        
    }
}
