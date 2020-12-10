@extends('backpack::blank')

@section('content')

    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <div class="card-header-actions">
                        <!-- Period selection dropdown -->
                        @include('partials.period_selection')
                    </div>
                </div>

                <div class="card-body">
                        <canvas id="myChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-md-12">
            <div class="card">
                <div class="card-header">@lang('Enrollments per Course')</div>

                <div class="card-body">
                    <table class="table table-striped">
                        <thead>
                            <th>@lang('Course')</th>
                            <th>@lang('Enrollments')</th>
                        </thead>

                        <tbody>
                            @foreach($courses as $course)
                                <tr>
                                    <td>
                                        {{ $course->name ?? '-' }}
                                    </td>

                                    <td>
                                        {{ $course->enrollments_count ?? '-' }}
                                    </td>
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

<script src="/js/app.js"></script>

<script>

$(document).ready(() => {

var data = @json($courses);
console.log(data);
var chartData = {
  labels: [],
  datasets: [{
	label: "Inscriptions",
	data: [],
	backgroundColor: '#ffc9d1'
  }]
};

for (s in data) {
    chartData.labels.push(data[s].name);
    chartData.datasets[0].data.push(data[s].enrollments_count);
}

	var ctx = $("#myChart");

	var barGraph = new Chart(ctx, {
		type: 'horizontalBar',
		data: chartData,
		options: {
			legend: { display: true },
			scales: {
         xAxes: [{
             ticks: {
                 beginAtZero: true
             }
         }]
     }
		}
	});


});

    </script>

@endsection
