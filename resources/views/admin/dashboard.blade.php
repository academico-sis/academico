@extends(backpack_view('blank'))

@section('after_styles')
<link href='/fullcalendar/core/main.css' rel='stylesheet' />
<link href='/fullcalendar/timegrid/main.css' rel='stylesheet' />
<link href='/fullcalendar/daygrid/main.css' rel='stylesheet' />

<script src='/fullcalendar/core/main.js'></script>
<script src='/fullcalendar/daygrid/main.js'></script>
<script src='/fullcalendar/timegrid/main.js'></script>
<script src='/fullcalendar/resource-common/main.js'></script>
<script src='/fullcalendar/resource-daygrid/main.js'></script>
<script src='/fullcalendar/resource-timegrid/main.js'></script>

@endsection


@section('header')
    <section class="container-fluid">
	  <h2>
        {{ trans('backpack::base.dashboard') }}
      </h2>
    </section>
@endsection


@section('content')

@include('reports.insights')


    <div class="row" id="app">


        @if($unassigned_teacher->count() > 0 && backpack_user()->can('hr.manage'))
        <div class="col-md-3">
            <div class="card">
                <div class="card-header with-border">
                    <div class="card-title">                          
                        <strong>@lang('Upcoming classes with no teacher assigned')</strong>
                    </div>
                    <div class="card-tools pull-right">

                    </div>
                </div>

                <div class="card-body">
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
            <div class="card">
                <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-user bg-primary p-3 font-2xl mr-3"></i>
                    <div>
                        <div class="text-value-sm text-primary">{{ $pending_leads }}</div>
                        <div class="text-muted text-uppercase font-weight-bold small">@lang('Pending leads')</div>
                    </div>
                </div>
                <div class="card-footer px-3 py-2"><a class="btn-block text-muted d-flex justify-content-between align-items-center" href='student?lead_status_is=["4"]'><span class="small font-weight-bold">@lang('View')</span><i class="fa fa-angle-right"></i></a></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-phone bg-primary p-3 font-2xl mr-3"></i>
                    <div>
                        <div class="text-value-sm text-primary">{{ $call_leads }}</div>
                        <div class="text-muted text-uppercase font-weight-bold small">@lang('Leads to call')</div>
                    </div>
                </div>
                <div class="card-footer px-3 py-2"><a class="btn-block text-muted d-flex justify-content-between align-items-center" href='student?lead_status_is=["5"]'><span class="small font-weight-bold">@lang('View')</span><i class="fa fa-angle-right"></i></a></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-check-square bg-primary p-3 font-2xl mr-3"></i>
                    <div>
                        <div class="text-value-sm text-primary">{{ $action_comments }}</div>
                        <div class="text-muted text-uppercase font-weight-bold small">@lang('Actionnable Comments')</div>
                    </div>
                </div>
                <div class="card-footer px-3 py-2"><a class="btn-block text-muted d-flex justify-content-between align-items-center" href='comment?action=true'><span class="small font-weight-bold">@lang('View')</span><i class="fa fa-angle-right"></i></a></div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-calendar bg-primary p-3 font-2xl mr-3"></i>
                    <div>
                        <div class="text-value-sm text-primary">{{ $pending_attendance }}</div>
                        <div class="text-muted text-uppercase font-weight-bold small">@lang('Pending Attendance')</div>
                    </div>
                </div>
                <div class="card-footer px-3 py-2"><a class="btn-block text-muted d-flex justify-content-between align-items-center" href="/attendance"><span class="small font-weight-bold">@lang('View')</span><i class="fa fa-angle-right"></i></a></div>
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
        plugins: [ 'dayGrid', 'resourceTimeGrid' ],
        defaultView: 'resourceTimeGridDay',
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
@endsection