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
                    <!-- Period selection dropdown -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ $selected_period->name }} <span class="caret"></span>
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
                
                <table class="table table-striped responsive" id="crudTable">
                    <thead>
                        <tr>
                            <th data-orderable="true">@lang('Teacher')</th>
                            <th>@lang('Planned Hours')</th>
                            <th>@lang('Period Max')</th>
                            <th><strong>@lang('Period Total')</strong></th>
                            <th><strong>@lang('% of period max')</strong></th>
                            <th>@lang('Worked Hours')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($teachers as $teacher)
                        @php
                            $max_hours = $teacher->period_max_hours($selected_period);
                            $period_hours = $teacher->period_planned_hours($selected_period);
                            $remote_hours = $teacher->periodRemoteHours($selected_period);
                        @endphp
                        <tr>
                            <td>{{ $teacher->name }}</td>
                            <td>
                                <p>@lang('Remote') : {{ number_format($remote_hours, 2, '.', ',') }} h</p>
                                <p>@lang('Presencial') : {{ number_format($period_hours, 2, '.', ',') }} h</p>
                            </td>

                            <td>{{ number_format($max_hours, 2, '.', ',') }} h</td>

                            <td>
                                <strong>{{ number_format($period_hours + $remote_hours, 2, '.', ',') }} h</strong>
                                
                                <div class="progress progress-xs">
                                    <div class="progress-bar progress-bar-red" style="width: {{100 * ($period_hours + $remote_hours)/$max_hours}}%"></div>
                              </div>
                            </td>

                            <td>{{ number_format(100 * ($period_hours + $remote_hours)/$max_hours, 0) }}%</td>

                            <td>{{ number_format($teacher->period_worked_hours($selected_period), 2, '.', ',') }} h</td>
                        </tr>
                        @endforeach
                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>
</div>
@endsection


@section('after_styles')
  <!-- DATA TABLES -->
  <link href="https://cdn.datatables.net/1.10.16/css/dataTables.bootstrap.min.css" rel="stylesheet" type="text/css" />
  <link rel="stylesheet" href="https://cdn.datatables.net/fixedheader/3.1.5/css/fixedHeader.dataTables.min.css">
  <link rel="stylesheet" href="https://cdn.datatables.net/responsive/2.2.1/css/responsive.bootstrap.min.css">

  <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/crud.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/form.css') }}">
  <link rel="stylesheet" href="{{ asset('vendor/backpack/crud/css/list.css') }}">

@endsection

@section('after_scripts')
<script src="https://cdn.datatables.net/1.10.16/js/jquery.dataTables.min.js" type="text/javascript"></script>
  <script src="https://cdn.datatables.net/1.10.16/js/dataTables.bootstrap.min.js" type="text/javascript"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.1/js/dataTables.responsive.min.js"></script>
  <script src="https://cdn.datatables.net/responsive/2.2.1/js/responsive.bootstrap.min.js"></script>
  <script src="
https://cdn.datatables.net/fixedheader/3.1.5/js/dataTables.fixedHeader.min.js"></script>

  <script src="{{ asset('vendor/backpack/crud/js/crud.js') }}"></script>
  <script src="{{ asset('vendor/backpack/crud/js/form.js') }}"></script>
  <script src="{{ asset('vendor/backpack/crud/js/list.js') }}"></script>

<script>
  $(document).ready( function () {
    $('#crudTable').DataTable({
        "pageLength": 50
    });
} );
</script>
@endsection

