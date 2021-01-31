<?php

namespace App\Providers;

use App\Events\CourseCreated;
use App\Events\CourseUpdated;
use App\Events\StudentCreated;
use App\Events\StudentDeleting;
use App\Events\StudentUpdated;
use App\Listeners\DeleteStudentData;
use App\Listeners\SyncCourseToLMS;
use App\Listeners\SyncStudentToLMS;
use App\Listeners\UpdateCourseEvents;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

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
        StudentCreated::class => [
            SyncStudentToLMS::class,
        ],
        StudentUpdated::class => [
            SyncStudentToLMS::class,
        ],
        StudentDeleting::class => [
            DeleteStudentData::class,
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
