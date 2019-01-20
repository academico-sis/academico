@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Human Resources')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                        @lang('Human Resources')
                </div>
                <div class="box-tools pull-right">
                    
                </div>
                
            </div>
            
            <div class="box-body">           
                
                <table class="table table-striped responsive">
                    <thead>
                        <tr>
                            <th>@lang('Teacher')</th>
                            <th>@lang('Remote Work')</th>
                            <th>@lang('Planned Hours')</th>
                            <th>@lang('Period Max')</th>
                            <th><strong>@lang('Period Total')</strong></th>
                            <th>@lang('Worked Hours')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($teachers as $teacher)
                        @php
                            $max_hours = $teacher->period_max_hours($period);
                            $period_hours = $teacher->period_planned_hours($period);
                            $remote_hours = $teacher->periodRemoteHours($period);
                        @endphp
                        <tr>
                            <td>{{ $teacher->name }}</td>
                            <td>{{ number_format($remote_hours, 2, '.', ',') }} h</td>
                            <td>{{ number_format($period_hours, 2, '.', ',') }} h</td>
                            <td>{{ number_format($max_hours, 2, '.', ',') }} h</td>
                            <td>
                                <strong>{{ number_format($period_hours + $remote_hours, 2, '.', ',') }} h</strong>
                                ({{ number_format(100 * ($period_hours + $remote_hours)/$max_hours, 0) }}%)
                            </td>
                            <td>{{ number_format($teacher->period_worked_hours($period), 2, '.', ',') }} h</td>
                        </tr>
                        @endforeach
                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
