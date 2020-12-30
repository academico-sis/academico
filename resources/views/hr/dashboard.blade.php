@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>@lang('Human Resources')</h2>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">@lang('Human Resources')
                <div class="card-header-actions">
                    <!-- Period selection dropdown -->
                    @include('partials.period_selection')
                </div>

            </div><!-- /.card-header -->

            <div class="card-body">

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
                            $remote_hours = $teacher->periodRemoteHours($selected_period)
                        @endphp
                        <tr>
                            <td>{{ $teacher->name }}</td>
                            <td>
                                <p>@lang('Remote') : {{ number_format($remote_hours, 2, '.', ',') }} h</p>
                                <p>@lang('Face-to-face') : {{ number_format($period_hours, 2, '.', ',') }} h</p>
                            </td>

                            <td>{{ number_format($max_hours, 2, '.', ',') }} h</td>

                            <td>
                                <strong>{{ number_format($period_hours + $remote_hours, 2, '.', ',') }} h</strong>

                                <div class="progress progress-xs">
                                    <div class="progress-bar progress-bar-red" style="width: {{(100 * ($period_hours + $remote_hours))/max(1, $max_hours)}}%"></div>
                              </div>
                            </td>

                            <td>{{ number_format((100 * ($period_hours + $remote_hours))/max(1, $max_hours), 0) }}%</td>

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
<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/dt-1.10.20/fh-3.1.6/datatables.min.css"/>
@endsection

@section('after_scripts')
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/dt-1.10.20/fh-3.1.6/datatables.min.js"></script>

<script>
  $(document).ready(() =>
      $('#crudTable').DataTable({
          "pageLength": 50
      }));
</script>
@endsection

