@extends('backpack::blank')

@section('content')

<div class="row">

    <div class="col-md-6">
        <div class="card">
            <div class="card-header" style="display: flex; justify-content: space-between; align-items: center">
                <div style="display: flex; flex-direction: column; align-items: center">
                    @lang('Enrollments per Level') - @if ($selected_period['type'] === 'year') {{ __('Year') }} @else {{ __('Period') }}@endif {{ $selected_period['value'] }}
                </div>
                <div class="card-header-actions">
                    <!-- Period selection dropdown -->
                    @include('partials.period_selection', ['allow_year_selection' => true, 'selected_period' => $selected_period])
                </div>
            </div>


            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <th>@lang('Level')</th>
                        <th>@lang('Enrollments')</th>
                        <th>@lang('Hours Taught')</th>
                        <th>@lang('Hours Sold')</th>
                    </thead>

                    <tbody>
                        @foreach($data as $level)
                        <tr>
                            <td>{{ $level['level'] ?? '-' }}</td>
                            <td>{{ $level['enrollment_count'] ?? '-' }}</td>
                            <td>{{ ($level['taught_hours_count'] . ' h') ?? '-' }}</td>
                            <td>{{ ($level['sold_hours_count'] . ' h') ?? '-' }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>


    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <canvas id="myChart"></canvas>
            </div>
        </div>
    </div>

</div>
@endsection

@section('before_scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.2/dist/chart.umd.min.js"></script>
@endsection


@section('after_scripts')

<script>
    function randomcolor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
    }


    $(document).ready(() => {

        var data = @json($data);

        var chartData = {
            labels: [],
            datasets: [{
                data: [],
                backgroundColor: []
            }]
        };

        for (s in data) {
            chartData.labels.push(data[s].level);
            chartData.datasets[0].data.push(data[s].enrollment_count);
            chartData.datasets[0].backgroundColor.push(randomcolor())
        }


        var ctx = $("#myChart");

        var barGraph = new Chart(ctx, {
            type: 'pie',
            data: chartData,
            options: {
                legend: {
                    display: true
                }
            }
        });
    });

</script>

@endsection
