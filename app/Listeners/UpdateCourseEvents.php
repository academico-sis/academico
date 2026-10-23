<?php

namespace App\Listeners;

use App\Events\CourseUpdated;
use App\Services\CourseEventScheduler;

class UpdateCourseEvents
{
    public function handle(CourseUpdated $event): void
    {
        app(CourseEventScheduler::class)->courseUpdated($event->course);
    }
}
