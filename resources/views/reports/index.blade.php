@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1>
        {{ trans('backpack::base.dashboard') }}
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a></li>
        <li class="active">{{ trans('backpack::base.dashboard') }}</li>
      </ol>
    </section>
@endsection


@section('content')

@include('reports.insights')

    <div class="row">



        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                    </div>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                
                <div class="box-body">
                        <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>


        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                    </div>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                
                <div class="box-body">
                    <table class="table table-striped">
                        <thead>
                            <th>@lang('Period')</th>
                            <th>@lang('Enrollments')</th>
                            <th>@lang('Students')</th>
                            <th>@lang('Acquisition Rate')(*)</th>
                            <th>@lang('New Students')</th>    
                            <th>@lang('Hours Taught')</th>
                            <th>@lang('Hours Sold')</th>
                        </thead>

                        @php
                            $current_year = \App\Models\Period::first()->year_id;
                            $i = 0;
                            $year_enrollments = 0;
                            $year_taught_hours = 0;
                            $year_sold_hours = 0;
                        @endphp

                        <tbody>
                            @foreach($data as $data_period)

                            {{-- if we are starting a new year, display previous year's totals first --}}
                            @if ($current_year != $data_period['year_id'])

                                <tr>
                                    <td>
                                        {{ \App\Models\Year::find($current_year)->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $year_enrollments ?? '-' }}
                                    </td>

                                    <td>
                                        {{ \App\Models\Year::find($current_year)->year_distinct_students_count ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $data_period['acquisition_rate'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $data_period['new_students'] ?? '-' }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($year_taught_hours ?? '0') }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($year_sold_hours ?? '0') }}
                                    </td>
                                </tr>

                                @php
                                    $i ++;
                                    $year_enrollments = 0;
                                    $year_taught_hours = 0;
                                    $year_sold_hours = 0;
                                @endphp
                                
                            @endif

                            @php
                                $year_enrollments += $data_period['enrollments'];
                                $year_taught_hours += $data_period['taught_hours'];
                                $year_sold_hours += $data_period['sold_hours'];
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
                                        {{ $data_period['new_students'] ?? '-' }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($data_period['taught_hours'] ?? '0') }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($data_period['sold_hours'] ?? '0') }}
                                    </td>
                                </tr>

                                @php
                                    $current_year = $data_period['year_id'];
                                    $i ++;
                                @endphp
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
    <script src="/js/Chart.min.js"></script>

    <script>
$(document).ready(function(){
var data = <?php echo json_encode($data) ?>;

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
			legend: {
				display: true
                },
                aspectRatio: '4'
		}
    });
});
            </script>

@endsection