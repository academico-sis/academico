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

        @if($unassigned_events > 0 && backpack_user()->can('hr.manage'))
        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-warning bg-danger p-3 font-2xl mr-3"></i>
                    <div>
                        <div class="text-value-sm text-danger">{{ $unassigned_events }}</div>
                        <div class="text-muted text-uppercase font-weight-bold small">@lang('Classes without teacher')</div>
                    </div>
                </div>
                <div class="card-footer px-3 py-2"><a class="btn-block text-muted d-flex justify-content-between align-items-center" href='event?unassigned=true&from_to={"from"%3A"@php echo date("Y-m-d") @endphp"%2C"to"%3A"@php echo date("Y-m-d", strToTime("+3 day")); @endphp"}'><span class="small font-weight-bold">@lang('View')</span><i class="fa fa-angle-right"></i></a></div>
            </div>
        </div>
        @endif

        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-user bg-danger p-3 font-2xl mr-3"></i>
                    <div>
                        <div class="text-value-sm text-danger">{{ $pending_leads }}</div>
                        <div class="text-muted text-uppercase font-weight-bold small">@lang('Pending leads')</div>
                    </div>
                </div>
                <div class="card-footer px-3 py-2"><a class="btn-block text-muted d-flex justify-content-between align-items-center" href='student?lead_status_is=4'><span class="small font-weight-bold">@lang('View')</span><i class="fa fa-angle-right"></i></a></div>
            </div>
        </div>


        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-check-square bg-danger p-3 font-2xl mr-3"></i>
                    <div>
                        <div class="text-value-sm text-danger">{{ $action_comments }}</div>
                        <div class="text-muted text-uppercase font-weight-bold small">@lang('Actionnable Comments')</div>
                    </div>
                </div>
                <div class="card-footer px-3 py-2"><a class="btn-block text-muted d-flex justify-content-between align-items-center" href='comment?action=true'><span class="small font-weight-bold">@lang('View')</span><i class="fa fa-angle-right"></i></a></div>
            </div>
        </div>

        <div class="col-lg-2 col-md-3 col-sm-6">
            <div class="card">
                <div class="card-body p-3 d-flex align-items-center"><i class="fa fa-calendar bg-danger p-3 font-2xl mr-3"></i>
                    <div>
                        <div class="text-value-sm text-danger">{{ $pending_attendance }}</div>
                        <div class="text-muted text-uppercase font-weight-bold small">@lang('Pending Attendance')</div>
                    </div>
                </div>
                <div class="card-footer px-3 py-2"><a class="btn-block text-muted d-flex justify-content-between align-items-center" href="/attendance"><span class="small font-weight-bold">@lang('View')</span><i class="fa fa-angle-right"></i></a></div>
            </div>
        </div>

    </div>

    <div class="row">
    <div class="col-md-12">

        <div class="card">
            <div class="box-body">
                <div class="chart-wrapper" style="padding: 10px;">
                        <div id="calendar"></div>
                </div>
            </div>
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