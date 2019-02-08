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
    
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('Enrollments per Rhythm')
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
    
</div>
@endsection


@section('before_scripts')

@endsection


@section('after_scripts')

<script src="/js/Chart.min.js"></script>

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
            var value = (hash >> (i * 8)) & 255;
            color += ('00' + value.toString(16)).substr(-2);
        }
        return color;
    }
    
    
    $(document).ready(function() {
        
        var data = <?php echo json_encode($data) ?>;
        
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