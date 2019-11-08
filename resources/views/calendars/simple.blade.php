@extends('backpack::blank')

@section('after_styles')
<link href='/fullcalendar/core/main.css' rel='stylesheet' />
<link href='/fullcalendar/daygrid/main.css' rel='stylesheet' />
<link href='/fullcalendar/timegrid/main.css' rel='stylesheet' />

<script src='/fullcalendar/core/main.js'></script>
<script src='/fullcalendar/daygrid/main.js'></script>
<script src='/fullcalendar/timegrid/main.js'></script>

@endsection

@section('header')
    <section class="container-fluid">
	  <h2>
        @lang('Calendar for') {{ $resource->name ?? "-" }}
      </h2>
    </section>
@endsection


@section('content')
    <div class="row">

        <div class="col-md-12">
            <div class="card">
                <div class="card-body">
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
