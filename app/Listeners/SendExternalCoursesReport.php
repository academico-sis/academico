<?php

namespace App\Listeners;

use App\Mail\ExternalCourseReport;
use App\Models\Partner;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class SendExternalCoursesReport
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function handle()
    {
        $period_start = Carbon::parse('first day of this month');
        $period_end = Carbon::parse('last day of this month');

        // foreach partner with an alert set for this day of month
        foreach (Partner::where('send_report_on', Carbon::now()->day)->get() as $partner) {
            // get all courses
            $courses = $partner->courses()->whereHas('events', function (Builder $query) use ($period_start, $period_end) {
                $query->where('start', '>=', $period_start->toDateString())->where('end', '<=', $period_end->toDateString());
            })
            ->with('events')->withCount('events')
            ->get();

            if ($courses->count() == 0) {
                return;
            }

            $data = [];

            $partner_balance = 0;
            foreach ($courses as $c => $course) {
                $data['courses'][$c]['course_name'] = $course->name;
                $data['courses'][$c]['hourly_price'] = $course->hourly_price;
                $data['courses'][$c]['hours_count'] = 0;
                foreach ($course->events->where('start', '>=', $period_start->toDateString())->where('end', '<=', $period_end->toDateString()) as $e => $event) {
                    $data['courses'][$c]['events'][$e] = $event->short_date.'('.$event->event_length.'h)';
                    $data['courses'][$c]['hours_count'] += $event->event_length;
                }
                $data['courses'][$c]['value'] = $data['courses'][$c]['hours_count'] * $course->hourly_price;
                $partner_balance += $data['courses'][$c]['value'];
            }
            $data['partner_name'] = $partner->name;
            $data['partner_balance'] = $partner_balance;

            Mail::to(config('settings.secretary_email'))->queue(new ExternalCourseReport($period_start, $period_end, $data));
        }
    }
}
