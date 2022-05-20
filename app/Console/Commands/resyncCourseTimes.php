<?php

namespace App\Console\Commands;

use App\Models\Course;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class resyncCourseTimes extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'academico:resync-coursetimes {course_ids*}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'In case events are not sync anomore with course times, re-create events for specified courses.';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        foreach ($this->argument('course_ids') as $courseId) {
            $course = Course::find($courseId);

            if (Carbon::parse($course->start_date) < Carbon::now()) {
                continue;
            }

            DB::table('events')->where('course_id', $courseId)->delete();

            $courseStartDate = Carbon::parse($course->start_date)->startOfDay();
            $courseEndDate = Carbon::parse($course->end_date)->startOfDay();

            while ($courseStartDate < $courseEndDate) {
                if ($course->times->contains('day', $courseStartDate->format('w'))) {
                    Event::create(['course_id' => $course->id, 'teacher_id' => $course->teacher_id, 'room_id' => $course->room_id, 'start' => $courseStartDate->setTimeFromTimeString($course->times->where('day', $courseStartDate->format('w'))->first()->start)->toDateTimeString(), 'end' => $courseStartDate->setTimeFromTimeString($course->times->where('day', $courseStartDate->format('w'))->first()->end)->toDateTimeString(), 'name' => $course->name, 'course_time_id' => $course->times->where('day', $courseStartDate->format('w'))->first()->id, 'exempt_attendance' => $course->exempt_attendance]);
                }

                $courseStartDate->addDay();
            }
        }

        return 0;
    }
}
