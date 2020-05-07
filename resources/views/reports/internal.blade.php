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
                        </thead>

                        @php
                            $current_year_id = $selected_period->year_id;
                            $i = 0;
                            $year_enrollments = 0;
                            $year_taught_hours = 0;
                            $year_sold_hours = 0;
                        @endphp

                        <tbody>
                            @foreach($data as $data_period)

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
                                    $current_year_id = $data_period['year_id'];
                                    $i ++;
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@2.9.2/dist/Chart.min.js"></script>

<script>
$(document).ready(function(){
var data = @json($data);

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

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/datatables.min.css"/>
 
 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
 <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
 <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/datatables.min.js"></script>
<script>
 $(document).ready(function() {
 $('#reportsTable').DataTable( {
 dom: 'Bfrtip',
 "paging": false,
 "searching": false,
    "ordering":  false,
 buttons: [
     'excel',
     'pdf',
     'print'
 ]
} );
} );
</script>
@endsection