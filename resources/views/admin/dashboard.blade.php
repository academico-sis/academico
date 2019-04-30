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

        @if(isset($pending_attendance) && backpack_user()->hasRole('admin'))
        <div class="col-md-3">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            <strong>
                                @lang('Pending Attendance')
                            </strong>
                        </div>
                        <div class="box-tools pull-right">
                        
                        </div>
                    </div>
    
                    <div class="box-body">
                        <ul>
                            @foreach($pending_attendance as $event)
                            <li>{{ $event['event'] }} ({{ $event['event_date'] }}) - <a href="{{ route('eventAttendance', ['event' => $event['event_id']]) }}">{{ $event['event_id'] }}</a></li>
                            @endforeach
                        </ul>
    
                    </div>
                </div>
            </div>
        @endif


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

        @if(backpack_user()->can('hr.manage'))
        <div class="col-md-3">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">                          
                            <strong>@lang('Human Resources')</strong>
                        </div>
                        <div class="box-tools pull-right">
    
                        </div>
                    </div>
    
                    <div class="box-body">
                        <p>@lang('Teachers') : {{ count($teachers) }}</p>
{{--                         <p>@lang('Upcoming Leaves') : <ul>
                            @foreach ($upcoming_leaves as $leave)
                                {{ $leave }}
                            @endforeach
                        </ul> --}}

                        {{-- nombre d'heures de cours aujourd'hui, cette semaine, ce mois, ce cycle --}}
                        {{-- comparaison par rapport au cycle dernier --}}
                    </div>
                </div>
            </div>
            @endif

            <div class="col-md-3">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">                          
                            <strong>@lang('Leads')</strong>
                        </div>
                        <div class="box-tools pull-right">
    
                        </div>
                    </div>
    
                    <div class="box-body">
                        <p>@lang('Pending leads') : {{ $pending_leads }}</p>
                        <p>@lang('Leads to call') : {{ $call_leads }}</p>

{{--                         <p>@lang('Upcoming Leaves') : <ul>
                            @foreach ($upcoming_leaves as $leave)
                                {{ $leave }}
                            @endforeach
                        </ul> --}}

                        {{-- nombre d'heures de cours aujourd'hui, cette semaine, ce mois, ce cycle --}}
                        {{-- comparaison par rapport au cycle dernier --}}
                    </div>
                </div>
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