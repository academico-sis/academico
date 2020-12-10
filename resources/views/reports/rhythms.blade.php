@extends('backpack::blank')

@section('content')

<div class="row">

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                    @lang('Enrollments per Rhythm')
                <div class="card-header-actions">
                    <!-- Period selection dropdown -->
                    @include('partials.period_selection')
                </div>
            </div>

            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <th>@lang('Rhythm')</th>
                        <th>@lang('Enrollments')</th>
                    </thead>

                    <tbody>
                        @foreach($data as $rhythm)
                        <tr>
                            <td>
                                {{ $rhythm['rhythm'] ?? '-' }}
                            </td>

                            <td>
                                {{ $rhythm['enrollment_count'] ?? '-' }}
                            </td>
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

<script src="/js/app.js"></script>

<script>
    String.prototype.toHex = function() {
        var hash = 0;
        if (this.length === 0) return hash;
        for (var i = 0; i < this.length; i++) {
            hash = this.charCodeAt(i) + ((hash << 5) - hash);
            hash = hash & hash;
        }
        var color = '#';
        for (var i = 0; i < 3; i++) {
            var value = hash >> (i * 8) & 255;
            color += (`00${value.toString(16)}`).substr(-2);
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
            chartData.labels.push(data[s].rhythm);
            chartData.datasets[0].data.push(data[s].enrollment_count);
            chartData.datasets[0].backgroundColor.push(data[s].rhythm.toHex())
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
