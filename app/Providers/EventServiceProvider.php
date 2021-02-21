<?php

namespace App\Providers;

use App\Events\CourseCreated;
use App\Events\CourseUpdated;
use App\Events\EnrollmentCreated;
use App\Events\EnrollmentDeleted;
use App\Events\EnrollmentUpdated;
use App\Events\UserCreated;
use App\Events\StudentDeleting;
use App\Events\UserDeleting;
use App\Events\UserUpdated;
use App\Listeners\AddPastAttendance;
use App\Listeners\DeleteEnrollmentFromLMS;
use App\Listeners\DeleteStudentData;
use App\Listeners\DeleteUserData;
use App\Listeners\DeleteStudentFromLMS;
use App\Listeners\SyncCourseToLMS;
use App\Listeners\SyncEnrollmentToLMS;
use App\Listeners\UpdateEnrollmentInLMS;
use App\Listeners\SyncUserToLMS;
use App\Listeners\UpdateCourseEvents;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\ExternalCoursesReportEvent;
use App\Listeners\SendExternalCoursesReport;
use App\Events\ExpiringPartnershipsEvent;
use App\Listeners\SendExpiringPartnershipsAlerts;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],

        CourseCreated::class => [
            SyncCourseToLMS::class,
        ],

        CourseUpdated::class => [
            UpdateCourseEvents::class,
            SyncCourseToLMS::class,
        ],

        UserCreated::class => [
            SyncUserToLMS::class,
        ],

        UserUpdated::class => [
            SyncUserToLMS::class,
        ],

        StudentDeleting::class => [
            DeleteStudentData::class,
            DeleteStudentFromLMS::class,
        ],

        UserDeleting::class => [
            DeleteUserData::class,
        ],

        EnrollmentDeleted::class => [
            DeleteEnrollmentFromLMS::class,
        ],

        EnrollmentCreated::class => [
            AddPastAttendance::class,
            SyncEnrollmentToLMS::class,
        ],

        EnrollmentUpdated::class => [
            UpdateEnrollmentInLMS::class
        ],
        ExternalCoursesReportEvent::class => [
            SendExternalCoursesReport::class
        ],
        ExpiringPartnershipsEvent::class => [
            SendExpiringPartnershipsAlerts::class
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        //
    }
}
