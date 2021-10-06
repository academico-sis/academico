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



@section('content')

<div class="row" style="align-items: center; text-align: center;">
    <div class="col-sm-6">
        <h2>Bonjour {{ backpack_user()->firstname }}</h2>
    </div>
    <div class="col-sm-6">
        @include('partials.default_periods_info')
    </div>
</div>

@include('reports.insights')

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
@endsection






@section('after_scripts')

<script>

document.addEventListener('DOMContentLoaded', () => { // page is now ready...
    var calendarEl = document.getElementById('calendar'); // grab element reference

    var calendar = new FullCalendar.Calendar(calendarEl, {
        plugins: [ 'dayGrid', 'resourceTimeGrid' ],
        defaultView: 'resourceTimeGridDay',
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        resources: @json($resources),
        height: "auto",
        nowIndicator: true,
        locale: "{{ backpack_user()->locale }}",
        events: @json($events),
        titleFormat: { // will produce something like "Tuesday, September 18, 2018"
            month: 'long',
            year: 'numeric',
            day: 'numeric',
            weekday: 'long'
        }

    });

    calendar.render();
});

</script>
@endsection
