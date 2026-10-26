<?php

namespace App\Listeners;

use App\Events\ExternalCoursesReportEvent;
use App\Mail\ExternalCourseReport;
use App\Models\Partner;
use App\Traits\ReportsErrors;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Mail;

class SendExternalCoursesReport
{
    use ReportsErrors;

    public function handle(ExternalCoursesReportEvent $event): void
    {
        // The report runs at night in the institution timezone, when the UTC date may differ.
        $today = Carbon::now(config('settings.courses_timezone'));
        $period_start = $today->copy()->startOfMonth();
        $period_end = $today->copy()->endOfMonth();

        foreach (Partner::where('send_report_on', $today->day)->get() as $partner) {
            try {
                $courses = $partner->courses()->whereHas('events', function (Builder $query) use ($period_start, $period_end) {
                    $query->where('start', '>=', $period_start->toDateString())->where('end', '<=', $period_end->toDateString());
                })
                    ->with('events')->withCount('events')
                    ->get();

                if ($courses->count() == 0) {
                    continue;
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
            } catch (\Throwable $e) {
                $this->reportError($e, 'SendExternalCoursesReport::handle', [
                    'partner_id' => $partner->id,
                ]);
            }
        }
    }
}
