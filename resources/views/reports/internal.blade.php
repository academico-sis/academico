@extends('backpack::blank')

@section('header')
    <section class="container-fluid">
	  <h2>
        {{ trans('backpack::base.dashboard') }}
      </h2>
    </section>
@endsection


@section('content')


    <div class="row">



        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
                    <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>


        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-actions">
                        <span>@lang('Start from period:')</span>
                        <!-- Period selection dropdown -->
                        @include('partials.period_selection')
                    </div>
                </div>

                <div class="card-body">
                    <table id ="reportsTable" class="table table-striped">
                        <thead>
                            <th>@lang('Period')</th>
                            <th>@lang('Enrollments')</th>
                            <th>@lang('Students')</th>
                            <th>@lang('Acquisition Rate')(*)</th>
                            <th>@lang('New Students')</th>
                            <th>@lang('Hours Taught')</th>
                            <th>@lang('Hours Sold')</th>
                            <th>@lang('Takings')</th>
                            <th>@lang('Average takings per hour taught')</th>
                        </thead>

                        @php
                            $current_year_id = $selected_period->year_id;
                            $i = 0;
                            $year_enrollments = 0;
                            $year_taught_hours = 0;
                            $year_sold_hours = 0;
                            $year_takings = 0;
                        @endphp

                        <tbody>
                            @foreach($data as $d => $data_period)

                            {{-- if we are starting a new year, display previous year's totals first --}}
                            @if ($current_year_id != $data_period['year_id'])

                                <tr style="font-weight: bold">
                                    <td>
                                        {{ $years[$current_year_id]->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $year_enrollments ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $years[$current_year_id]->year_distinct_students_count ?? '-' }}
                                    </td>

                                    <td>
                                        -
                                    </td>

                                    <td>
                                        -
                                    </td>

                                    <td>
                                        {{ number_format($year_taught_hours ?? '0') }}
                                    </td>

                                    <td>
                                        {{ number_format($year_sold_hours ?? '0') }}
                                    </td>

                                    <td>
                                        @if (config('app.currency_position') === 'before')
                                            {{ config('app.currency_symbol') }} {{ number_format($year_takings) }}
                                        @else
                                            {{ number_format($year_takings) }} {{ config('app.currency_symbol') }}
                                        @endif
                                    </td>

                                    <td>
                                        @if (config('app.currency_position') === 'before')
                                            {{ config('app.currency_symbol') }} {{ number_format($year_takings / $year_taught_hours, 2) }}
                                        @else
                                            {{ number_format($year_takings / $year_taught_hours, 2) }} {{ config('app.currency_symbol') }}
                                        @endif
                                    </td>
                                </tr>

                                @php
                                    $i ++;
                                    $year_enrollments = 0;
                                    $year_taught_hours = 0;
                                    $year_sold_hours = 0;
                                    $year_takings = 0;
                                @endphp

                            @endif

                            @php
                                $year_enrollments += $data_period['enrollments'];
                                $year_taught_hours += $data_period['taught_hours'];
                                $year_sold_hours += $data_period['sold_hours'];
                                $year_takings += $data_period['takings'];
                            @endphp

                                <tr>
                                    <td>
                                        {{ $data_period['period'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $data_period['enrollments'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $data_period['students'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $data_period['acquisition_rate'] ?? '-' }}
                                    </td>

                                    <td>
                                        <a href="{{ route('student.index', ['new_students' => $d]) }}">
                                            {{ $data_period['new_students'] ?? '-' }}
                                        </a>
                                    </td>

                                    <td>
                                        {{ number_format($data_period['taught_hours'] ?? '0') }}
                                    </td>

                                    <td>
                                        {{ number_format($data_period['sold_hours'] ?? '0') }}
                                    </td>

                                    <td>
                                        @if (config('app.currency_position') === 'before')
                                            {{ config('app.currency_symbol') }} {{ number_format($data_period['takings'] ?? '-') }}
                                        @else
                                            {{ number_format($data_period['takings'] ?? '-') }} {{ config('app.currency_symbol') }}
                                        @endif
                                    </td>

                                    <td>
                                        @if (config('app.currency_position') === 'before')
                                            {{ config('app.currency_symbol') }} {{ number_format($data_period['avg_takings'] ?? '-', 2) }}
                                        @else
                                            {{ number_format($data_period['avg_takings'] ?? '-', 2) }} {{ config('app.currency_symbol') }}
                                        @endif
                                    </td>
                                </tr>

                                @php
                                    $current_year_id = $data_period['year_id'];
                                    $i ++
                                @endphp
                            @endforeach

                            <tr style="font-weight: bold">
                                    <td>
                                        {{ $years[$current_year_id]->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $year_enrollments ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $years[$current_year_id]->year_distinct_students_count ?? '-' }}
                                    </td>

                                    <td>
                                        -
                                    </td>

                                    <td>
                                        -
                                    </td>

                                    <td>
                                        {{ number_format($year_taught_hours ?? '0') }}
                                    </td>

                                    <td>
                                        {{ number_format($year_sold_hours ?? '0') }}
                                    </td>

                                    <td>
                                        @if (config('app.currency_position') === 'before')
                                            {{ config('app.currency_symbol') }} {{ number_format($year_takings ?? '-') }}
                                        @else
                                            {{ number_format($year_takings ?? '-') }} {{ config('app.currency_symbol') }}
                                        @endif

                                    </td>

                                    <td>
                                        @if (config('app.currency_position') === 'before')
                                            {{ config('app.currency_symbol') }} {{ number_format($year_takings / $year_taught_hours, 2) }}
                                        @else
                                            {{ number_format($year_takings / $year_taught_hours, 2) }} {{ config('app.currency_symbol') }}
                                        @endif

                                    </td>
                                </tr>

                        </tbody>
                    </table>
                    <p>(*) = @lang('share of students from previous period who were re-enrolled')</p>
                </div>
            </div>
        </div>

    </div>
@endsection


@section('before_scripts')

@endsection


@section('after_scripts')

<script>
$(document).ready(() => {
var data = @json(array_values($data));

var chartData = {
  labels: [],
  datasets: [
    {
        label: "Apprenants différents",
        data: [],
        backgroundColor: '#98d1f1',
        borderColor: '#5b76d8'
    },
    {
        label: "Inscriptions",
        data: [],
        borderColor: '#dd4b39',
        backgroundColor: '#ffc9d1'
    }
]
};

for (s in data) {
    chartData.labels.push(data[s].period);
    chartData.datasets[1].data.push(data[s].enrollments);
    chartData.datasets[0].data.push(data[s].students);
}

	var ctx = document.getElementById("myChart");

	var myChart = new Chart(ctx, {
		type: 'line',
		data: chartData,
		options: {
            scales: {
                yAxes: [{
                    ticks: {
                        beginAtZero: true,
                    }
                }]
            },
			legend: {
				display: true
                },
                aspectRatio: '4'
		}
    });
});
            </script>

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/datatables.min.css"/>

 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
 <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/datatables.min.js"></script>
<script>
 $(document).ready(() =>
     $('#reportsTable').DataTable({
         dom: 'Bfrtip',
         "paging": false,
         "searching": false,
         "ordering": false,
         buttons: [
             'excel',
             'pdf',
             'print'
         ]
     }));
</script>
@endsection
