@extends('backpack::layout')

@section('after_styles')
<link href='/fullcalendar/core/main.css' rel='stylesheet' />
<link href='/fullcalendar/daygrid/main.css' rel='stylesheet' />
<link href='/fullcalendar/timegrid/main.css' rel='stylesheet' />

<script src='/fullcalendar/core/main.js'></script>
<script src='/fullcalendar/daygrid/main.js'></script>
<script src='/fullcalendar/timegrid/main.js'></script>

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
    <div class="row">

        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">                          
                        <strong>@lang('Calendar for') {{ $resource->name }}</strong>
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
    plugins: [ 'timeGrid' ],
    defaultView: 'timeGridWeek',
    minTime: "06:00:00",
    maxTime: "23:00:00",
    nowIndicator: true,
    hiddenDays: [ 0 ],
    firstDay: 1,
    eventSources: [
            {
                events: <?php echo json_encode($events) ?>,
            },
            {
                events: <?php echo json_encode($leaves ?? null) ?>,
                color: 'red',
                textColor: 'white',
            },
        ],
});

    calendar.render();
    });

</script>
@endsection
