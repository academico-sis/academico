<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Models\Config;
use App\Models\Partner;
use App\Models\Period;
use App\Models\Year;
use App\Services\DateRange;
use App\Services\StatService;
use App\Traits\PeriodSelection;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ExternalReportController extends Controller
{
    use PeriodSelection;

    public function external2(Request $request)
    {
        $data = [];
        $report_start_date = Carbon::parse($request->report_start_date) ?? Carbon::parse('2019-01-01');
        $report_end_date = Carbon::parse($request->report_end_date) ?? Carbon::now();

        $stats = new StatService(external: true, reference: new DateRange($report_start_date, $report_end_date), partner: null);

        $data['courses'] = $stats->coursesCount();
        $data['enrollments'] = $stats->enrollmentsCount();
        $data['students'] = $stats->studentsCount();
        $data['taught_hours'] = $stats->taughtHoursCount();
        $data['sold_hours'] = $stats->soldHoursCount();

        return view('reports.external2', [
            'start' => Carbon::parse($report_start_date)->format('Y-m-d'),
            'end' => Carbon::parse($report_end_date)->format('Y-m-d'),
            'data' => $data,
            'courses' => $stats->coursesQuery->get(),
        ]);
    }

    public function external(Request $request)
    {
        $data = [];
        $year_data = [];
        $years = [];

        if (! isset($request->period)) {
            $startperiod = Period::find(Config::where('name', 'first_external_period')->first()->value ?? Period::first()->id);
        } else {
            $startperiod = Period::find($request->period);
        }

        $selectedPartner = Partner::find($request->partner);

        $periods = Period::where('id', '>=', $startperiod->id)->get();

        $current_year_id = $startperiod->year_id;
        $year_data[$current_year_id] = [];
        $year_data[$current_year_id]['year_name'] = Year::find($current_year_id)->name;
        $year_data[$current_year_id]['students'] = 0;
        $year_data[$current_year_id]['enrollments'] = 0;
        $year_data[$current_year_id]['taught_hours'] = 0;
        $year_data[$current_year_id]['sold_hours'] = 0;

        foreach ($periods as $data_period) {
            $periodStats = new StatService(external: true, reference: $data_period, partner: $selectedPartner);

            $data[$data_period->id]['period'] = $data_period->name;
            $data[$data_period->id]['year_id'] = $data_period->year_id;
            $data[$data_period->id]['courses'] = $periodStats->coursesCount();
            $data[$data_period->id]['partnerships'] = $periodStats->partnershipsCount();
            $data[$data_period->id]['enrollments'] = $periodStats->enrollmentsCount();
            $data[$data_period->id]['students'] = $periodStats->studentsCount();
            $data[$data_period->id]['taught_hours'] = $periodStats->taughtHoursCount();
            $data[$data_period->id]['sold_hours'] = $periodStats->soldHoursCount();

            // if we are starting a new year, push the year data to the array
            if ($current_year_id != $data_period->year_id) {
                $current_year_id = $data_period->year_id;

                $year_data[$current_year_id] = [];
                $year_data[$current_year_id]['year_name'] = Year::find($current_year_id)->name;
                $year_data[$current_year_id]['students'] = 0;
                $year_data[$current_year_id]['enrollments'] = 0;
                $year_data[$current_year_id]['taught_hours'] = 0;
                $year_data[$current_year_id]['sold_hours'] = 0;
            }

            $year_data[$current_year_id]['students'] += $data[$data_period->id]['students'];
            $year_data[$current_year_id]['enrollments'] += $data[$data_period->id]['enrollments'];
            $year_data[$current_year_id]['taught_hours'] += $data[$data_period->id]['taught_hours'];
            $year_data[$current_year_id]['sold_hours'] += $data[$data_period->id]['sold_hours'];

            $year = Year::find($data_period->year_id)->append('partnerships');

            $yearStats = new StatService(external: true, reference: $year, partner: $selectedPartner);
            $years[$data_period->year_id]['year'] = $year->name;
            $years[$data_period->year_id]['partnerships'] = $yearStats->partnershipsCount();
        }

        return view('reports.external', [
            'selected_period' => $startperiod,
            'selected_partner' => $selectedPartner,
            'data' => $data,
            'year_data' => $year_data,
            'years' => $years,
            'partners' => Partner::all(),
        ]);
    }

    public function external3()
    {
        return view('reports.external3', [
            'partners' => Partner::all(),
        ]);
    }

    public function partner(Partner $partner, Request $request)
    {
        $data = [];
        $report_start_date = Carbon::parse($request->report_start_date) ?? Carbon::parse('2019-01-01');
        $report_end_date = Carbon::parse($request->report_end_date) ?? Carbon::now();

        $stats = new StatService(external: true, reference: new DateRange($report_start_date, $report_end_date), partner: $partner);

        $data['courses'] = $stats->coursesCount();
        $data['enrollments'] = $stats->enrollmentsCount();
        $data['students'] = $stats->studentsCount();
        $data['taught_hours'] = $stats->taughtHoursCount();
        $data['sold_hours'] = $stats->soldHoursCount();

        return view('reports.partner', [
            'partner' => $partner,
            'start' => Carbon::parse($report_start_date)->format('Y-m-d'),
            'end' => Carbon::parse($report_end_date)->format('Y-m-d'),
            'data' => $data,
        ]);
    }
}
