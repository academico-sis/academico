<?php

namespace App\Providers;

use App\Events\CourseCreated;
use App\Events\CourseUpdated;
use App\Listeners\SyncCourseToLMS;
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
