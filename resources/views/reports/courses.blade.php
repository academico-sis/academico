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


    <div class="row">



        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                    </div>
                    <div class="box-tools pull-right">
                        <!-- Period selection dropdown -->
                        <div class="btn-group">
                            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{ $period->name }} <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu">
                                @foreach ($periods as $period)
                                <li><a href="{{ url()->current() }}/?period={{ $period->id }}">{{ $period->name }}</a></li>
                                @endforeach
                            </ul>
                        </div> 

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
                        @lang('Enrollments per Course')
                    </div>
                    <div class="box-tools pull-right">

                    </div>
                </div>
                
                <div class="box-body">
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

    <script src="/js/Chart.min.js"></script>

    <script>

$(document).ready(function(){

var data = <?php echo json_encode($courses) ?>;
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