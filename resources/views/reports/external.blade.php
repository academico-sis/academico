@extends('backpack::blank')

@section('header')
    <section class="container-fluid">
	  <h2>@lang('External Courses Report')</h2>
    </section>
@endsection


@section('content')
    <div class="row">

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <canvas id="periodsChart"></canvas>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <canvas id="yearsChart"></canvas>
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
                    <table class="table table-striped">
                        <thead>
                            <th>@lang('Period')</th>
                            <th>@lang('Number of Courses')</th>
                            <th>@lang('Number of Partnerships')</th>
                            <th>@lang('Enrollments')</th>
                            <th>@lang('New Students')</th>    
                            <th>@lang('Hours Taught')</th>
                            <th>@lang('Hours Sold')</th>
                        </thead>

                        @php
                            $current_year_id = $selected_period->year_id;
                            $i = 0;
                            $year_enrollments = 0;
                            $year_taught_hours = 0;
                            $year_sold_hours = 0;
                            $year_students = 0;
                            $year_courses = 0;
                        @endphp

                        <tbody>
                            @foreach($data as $data_period)

                            {{-- if we are starting a new year, display previous year's totals first --}}
                            @if ($current_year_id != $data_period['year_id'])

                                <tr style="font-weight: bold">
                                    <td>
                                        {{ $years[$current_year_id]['year'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $year_courses ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $years[$current_year_id]['partnerships'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $year_enrollments ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $year_students ?? '-' }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($year_taught_hours ?? '0', 0, ',', ' ') }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($year_sold_hours ?? '0', 0, ',', ' ') }}
                                    </td>
                                </tr>

                                @php
                                    $i ++;
                                    $year_enrollments = 0;
                                    $year_students = 0;
                                    $year_courses = 0;
                                    $year_taught_hours = 0;
                                    $year_sold_hours = 0;
                                @endphp
                                
                            @endif

                            @php
                                $year_courses += $data_period['courses'];
                                $year_enrollments += $data_period['enrollments'];
                                $year_students += $data_period['students'];
                                $year_taught_hours += $data_period['taught_hours'];
                                $year_sold_hours += $data_period['sold_hours'];
                            @endphp

                                <tr>
                                    <td>
                                        {{ $data_period['period'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $data_period['courses'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $data_period['partnerships'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $data_period['enrollments'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $data_period['students'] ?? '-' }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($data_period['taught_hours'] ?? '0', 0, ',', ' ') }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($data_period['sold_hours'] ?? '0', 0, ',', ' ') }}
                                    </td>
                                </tr>

                                @php
                                    $current_year_id = $data_period['year_id'];
                                    $i ++;
                                @endphp
                            @endforeach

                            <tr style="font-weight: bold">
                                    <td>
                                        {{ $years[$current_year_id]['year'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $year_courses ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $years[$current_year_id]['partnerships'] ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $year_enrollments ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $year_students ?? '-' }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($year_taught_hours ?? '0', 0, ',', ' ') }}
                                    </td>
                                    
                                    <td>
                                        {{ number_format($year_sold_hours ?? '0', 0, ',', ' ') }}
                                    </td>
                                </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>


    </div>
@endsection


@section('before_scripts')
   
@endsection


@section('after_scripts')
<script>
$(document).ready(function()
{
    var data = @json($data);
    var yearData = @json($year_data);

    var periodsChartData = {
        labels: [],
        datasets: [
        {
            label: "Inscriptions",
            data: [],
            backgroundColor: '#F0E52D',
        },
        {
            label: "Heures-prof",
            data: [],
            backgroundColor: '#ffa600',
        },
        ]
    };

    for (s in data) {
        periodsChartData.labels.push(data[s]["period"]);
        periodsChartData.datasets[0].data.push(data[s].enrollments);
        periodsChartData.datasets[1].data.push(data[s].taught_hours);
    }

    var periodsChart = new Chart(document.getElementById("periodsChart"), {
        type: 'bar',
        data: periodsChartData,
        options: {
            legend: {
                display: true
            },
        }
    });

    var yearsChartData = {
        labels: [],
        datasets: [
        {
            label: "Apprenants différents",
            data: [],
            backgroundColor: '#577BF0',
            yAxisID: "y-axis-0",
        },
        {
            label: "Heures-élève",
            data: [],
            backgroundColor: '#bc5090',
            yAxisID: "y-axis-1",
        },
        ]
    };

    for (s in yearData) {
        yearsChartData.labels.push(yearData[s]["year_name"]);
        yearsChartData.datasets[0].data.push(yearData[s].students);
        yearsChartData.datasets[1].data.push(yearData[s].sold_hours);
    }

    var yearsChart = new Chart(document.getElementById("yearsChart"), {
        type: 'bar',
        data: yearsChartData,
        options: {
            legend: {
                display: true
            },
            scales: {
                xAxes: [{
                    stacked: false
                }],
                yAxes: [{
                    position: "left",
                    id: "y-axis-0",
                    type: 'linear',
                    ticks: {
                        min: 0,
                        max: Math.max.apply(Math, yearsChartData.datasets[0].data.map(function(o) { return o; }))
                    }
                }, {
                    position: "right",
                    id: "y-axis-1",
                    type: 'linear',
                    ticks: {
                        min: 0,
                        max: Math.max.apply(Math, yearsChartData.datasets[1].data.map(function(o) { return o; }))
                    }
                }]
            }
        }
    });
});
</script>

@endsection