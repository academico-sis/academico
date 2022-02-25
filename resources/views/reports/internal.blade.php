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
                            @if (config('academico.include_takings_in_reports'))
                                <th>@lang('Takings')</th>
                                <th>@lang('Average takings per hour taught')</th>
                            @endif
                        </thead>

                        <tbody>
                        @foreach($data as $year)
                            @foreach($year['periods'] as $p => $period)

                                <tr>
                                    <td>
                                        {{ $period['period'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $period['enrollments'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $period['students'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $period['acquisition_rate'] ?? '-' }}
                                    </td>

                                    <td>
                                        <a href="{{ route('student.index', ['new_students' => $p]) }}">
                                            {{ $period['new_students'] ?? '-' }}
                                        </a>
                                    </td>

                                    <td>
                                        {{ number_format($period['taught_hours'] ?? '0') }}
                                    </td>

                                    <td>
                                        {{ number_format($period['sold_hours'] ?? '0') }}
                                    </td>

                                    @if (config('academico.include_takings_in_reports'))
                                        <td>
                                            @if (config('academico.currency_position') === 'before')
                                                {{ config('academico.currency_symbol') }} {{ number_format($period['takings'] ?? '-') }}
                                            @else
                                                {{ number_format($period['takings'] ?? '-') }} {{ config('academico.currency_symbol') }}
                                            @endif
                                        </td>

                                        <td>
                                            @if (config('academico.currency_position') === 'before')
                                                {{ config('academico.currency_symbol') }} {{ number_format($period['avg_takings'] ?? '-', 2) }}
                                            @else
                                                {{ number_format($period['avg_takings'] ?? '-', 2) }} {{ config('academico.currency_symbol') }}
                                            @endif
                                        </td>
                                    @endif
                                </tr>
                            @endforeach

                            <tr style="font-weight: bold">
                                    <td>
                                        {{ $year['year']['name'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $year['year']['enrollments'] }}
                                    </td>

                                    <td>
                                        {{ $year['year']['students'] }}
                                    </td>

                                    <td>
                                        -
                                    </td>

                                    <td>
                                        -
                                    </td>

                                    <td>
                                        {{ number_format($year['year']['taught_hours'] ?? '0') }}
                                    </td>

                                    <td>
                                        {{ number_format($year['year']['sold_hours'] ?? '0') }}
                                    </td>

                                    @if (config('academico.include_takings_in_reports'))
                                        <td>
                                            @if (config('academico.currency_position') === 'before')
                                                {{ config('academico.currency_symbol') }} {{ number_format($year['year']['takings'] ?? '-') }}
                                            @else
                                                {{ number_format($year['year']['takings'] ?? '-') }} {{ config('academico.currency_symbol') }}
                                            @endif
                                        </td>

                                        <td>
                                            @if ($year['year']['avg_takings'])
                                                @if (config('academico.currency_position') === 'before')
                                                    {{ config('academico.currency_symbol') }} {{ number_format($year['year']['avg_takings'], 2) }}
                                                @else
                                                    {{ number_format($year['year']['avg_takings'], 2) }} {{ config('academico.currency_symbol') }}
                                                @endif
                                            @endif
                                        </td>
                                @endif
                                </tr>
                        @endforeach
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
    var data = @json($data->pluck('periods')->flatten(1));

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
