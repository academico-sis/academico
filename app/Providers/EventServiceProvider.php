<?php

namespace App\Providers;

use App\Events\CourseCreated;
use App\Events\CourseUpdated;
use App\Events\EnrollmentCourseUpdated;
use App\Events\EnrollmentCreated;
use App\Events\EnrollmentDeleted;
use App\Events\EnrollmentUpdated;
use App\Events\EnrollmentUpdating;
use App\Events\ExpiringPartnershipsEvent;
use App\Events\ExternalCoursesReportEvent;
use App\Events\LeadStatusUpdatedEvent;
use App\Events\MonthlyReportEvent;
use App\Events\StudentDeleted;
use App\Events\StudentDeleting;
use App\Events\UserCreated;
use App\Events\StudentDeleting;
use App\Events\UserDeleting;
use App\Events\UserUpdated;
use App\Listeners\AddPastAttendance;
use App\Listeners\CleanChildrenEnrollments;
use App\Listeners\DeleteStudentData;
use App\Listeners\DeleteUserData;
use App\Listeners\SendExpiringPartnershipsAlerts;
use App\Listeners\SendExternalCoursesReport;
use App\Listeners\SendMonthlyReport;
use App\Listeners\SyncUserWithMailingSystem;
use App\Listeners\UpdateChildrenEnrollments;
use App\Listeners\UpdateCourseEvents;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Listeners\ComputeStudentLeadStatus;

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

        CourseUpdated::class => [
            UpdateCourseEvents::class,
        ],

        EnrollmentUpdating::class => [
            CleanChildrenEnrollments::class,
        ],

        EnrollmentUpdated::class => [
            UpdateChildrenEnrollments::class,
            ComputeStudentLeadStatus::class,
        ],

        StudentDeleting::class => [
            DeleteStudentData::class,
        ],

        UserDeleting::class => [
            DeleteUserData::class,
        ],
        EnrollmentDeleted::class => [
            ComputeStudentLeadStatus::class,
        ],
        EnrollmentCreated::class => [
            AddPastAttendance::class,
            ComputeStudentLeadStatus::class,

        ],

        ExternalCoursesReportEvent::class => [
            SendExternalCoursesReport::class,
        ],

        ExpiringPartnershipsEvent::class => [
            SendExpiringPartnershipsAlerts::class,
        ],

        MonthlyReportEvent::class => [
            SendMonthlyReport::class,
        ],

        LeadStatusUpdatedEvent::class => [
            SyncUserWithMailingSystem::class,
        ],
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
