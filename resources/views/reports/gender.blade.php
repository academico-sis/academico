@extends('backpack::blank')

@section('header')
    <section class="container-fluid">
	  <h2>
        {{ __('Students by gender') }}
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
                            <th>@lang('Year')</th>
                            <th>@lang('Period')</th>
                            <th>@lang('Female students')</th>
                            <th>@lang('Male students')</th>
                            <th>@lang('Students of unknown gender')</th>
                        </thead>

                        @php $current_year_id = $selected_period->year_id; @endphp

                        <tbody>
                            @foreach($data as $year)
                                @foreach($year['periods'] as $period)
                                    <tr>
                                        <td></td>
                                        <td>{{ $period['period'] }}</td>
                                        <td>
                                            @if ($period['unknown'] < 25) {{ number_format($period['female']) }} %
                                            @else {{__('Insufficient data')}} @endif
                                        </td>
                                        <td>
                                            @if ($period['unknown'] < 25) {{ number_format($period['male']) }} %
                                            @else {{__('Insufficient data')}} @endif
                                        </td>
                                        <td>
                                            <span class="{{ $period['unknown'] < 25 ? '' : 'text-danger' }}">{{ number_format($period['unknown']) }} %</span>

                                        </td>
                                    </tr>
                                @endforeach
                                <tr style="font-weight: bold">
                                    <td>{{ $year['year'] }}</td>
                                    <td></td>
                                    <td>{{ number_format($year['female']) }} %</td>
                                    <td>{{ number_format($year['male']) }} %</td>
                                    <td>{{ number_format($year['unknown']) }} %</td>
                                </tr>
                            @endforeach

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
        $(document).ready(() => {
            var data = @json($data->pluck('periods')->flatten(1));
            var axisLabel = @json(__('% of students in period'));
            var femaleLabel = @json(__('Female students'));
            var maleLabel = @json(__('Male students'));

            var chartData = {
                labels: [],
                datasets: [
                    {
                        label: femaleLabel,
                        data: [],
                        backgroundColor: 'rgba(245,255,152,0.6)',
                        borderColor: '#f5e700'
                    },
                    {
                        label: maleLabel,
                        data: [],
                        borderColor: '#cb47fc',
                        backgroundColor: 'rgba(230,176,255,0.6)'
                    }
                ]
            };

            for (s in data) {
                if (data[s].unknown < 25) {
                    chartData.labels.push(data[s].period);
                    chartData.datasets.filter(s => s.label === femaleLabel)[0].data.push(data[s].female);
                    chartData.datasets.filter(s => s.label === maleLabel)[0].data.push(data[s].male);
                }
            }

            var ctx = document.getElementById("myChart");

            var myChart = new Chart(ctx, {
                type: 'line',
                data: chartData,
                options: {
                    scales: {
                        yAxes: [{
                            scaleLabel: {
                                labelString: axisLabel,
                                display: true,
                            },
                            ticks: {
                                beginAtZero: true,
                                max: 100,
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
