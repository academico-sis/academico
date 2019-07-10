@extends('backpack::layout')


@section('after_styles')
<link rel='stylesheet' href='/css/fullcalendar.min.css' />
<link rel='stylesheet' href='/css/scheduler.min.css' />

<script src='/js/fullcalendar.min.js'></script>
<script src='/js/scheduler.min.js'></script>

@endsection


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

@include('reports.insights')


    <div class="row">


        @if($unassigned_teacher->count() > 0 && backpack_user()->can('hr.manage'))
        <div class="col-md-3">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">                          
                        <strong>@lang('Upcoming classes with no teacher assigned')</strong>
                    </div>
                    <div class="box-tools pull-right">

                    </div>
                </div>

                <div class="box-body">
                    <ul>
                        @foreach ($unassigned_teacher as $event)
                            <li>{{ $event->name }} ({{ $event->start }})</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
        @endif



        <div class="col-md-3">
            <a href='student?lead_status_is=["4"]'>
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-question"></i></span>
                    <div class="info-box-content">
                            <span class="info-box-text">@lang('Pending leads')</span>
                            <span class="info-box-number">{{ $pending_leads }}</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href='student?lead_status_is=["5"]'>
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-phone"></i></span>
                    <div class="info-box-content">
                            <span class="info-box-text">@lang('Leads to call')</span>
                            <span class="info-box-number">{{ $call_leads }}</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href='student?lead_status_is=["5"]'>
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-check-square"></i></span>
                    <div class="info-box-content">
                            <span class="info-box-text">@lang('Actionnable Comments')</span>
                            <span class="info-box-number">{{ $pending_leads }}</span>
                    </div>
                </div>
            </a>
        </div>

        <div class="col-md-3">
            <a href="/attendance">
                <div class="info-box">
                    <span class="info-box-icon bg-green"><i class="fa fa-calendar"></i></span>
                    <div class="info-box-content">
                            <span class="info-box-text">@lang('Pending Attendance')</span>
                            <span class="info-box-number">{{ $pending_attendance }}</span>
                    </div>
                </div>
            </a>
        </div>


    </div>

    <div class="row">

        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">                          
                        <strong>@lang('resource Calendars')</strong>
                    </div>
                    <div class="box-tools pull-right">

                    </div>
                </div>

                <div class="box-body">
                    <div id="calendar"></div>
                </div>
            </div>
        </div>

    </div>
@endsection






@section('after_scripts')
    
<script>

document.addEventListener('DOMContentLoaded', function() { // page is now ready...
    var calendarEl = document.getElementById('calendar'); // grab element reference

    var calendar = new FullCalendar.Calendar(calendarEl, {
        defaultView: 'agendaDay',
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        resources: <?php echo json_encode($resources) ?>,
        height: "auto",
        minTime: "07:00:00",
        maxTime: "21:00:00",
        nowIndicator: true,
        locale: 'fr',
        events: <?php echo json_encode($events) ?>

    });

    calendar.render();
});

</script>
    <script src="/js/app.js"></script>
@endsection