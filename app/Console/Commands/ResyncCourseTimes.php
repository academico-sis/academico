<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Services\CourseEventScheduler;
use Illuminate\Console\Command;

class ResyncCourseTimes extends Command
{
    protected $signature = 'academico:resync-coursetimes {course_ids*}';

    protected $description = 'In case events are not in sync with course times, create the missing events and delete those outside the schedule (events with attendance are kept).';

    public function handle(): int
    {
        foreach ($this->argument('course_ids') as $courseId) {
            $course = Course::find($courseId);

            if (! $course) {
                $this->error("Course {$courseId} not found.");

                continue;
            }

            app(CourseEventScheduler::class)->resync($course);

            $this->info("Course {$courseId} events re-synced.");
        }

        return Command::SUCCESS;
    }
}
