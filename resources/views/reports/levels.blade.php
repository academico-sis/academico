@extends('backpack::blank')

@section('content')

<div class="row">

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                    @lang('Enrollments per Level')
                <div class="card-header-actions">
                    <!-- Period selection dropdown -->
                    @include('partials.period_selection')
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
