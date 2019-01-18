@extends('backpack::layout')

@section('after_styles')
<link rel='stylesheet' href='/css/fullcalendar.min.css' />
<script src='/js/fullcalendar.min.js'></script>

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
                        <strong>@lang('Calendar for') {{ $ressource->name }}</strong>
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
    defaultView: 'agendaWeek',
    minTime: "06:00:00",
    maxTime: "23:00:00",
    nowIndicator: true,
    hiddenDays: [ 0 ],
    firstDay: 1,
    eventSources: [
    {
        events: <?php echo json_encode($events) ?>
    },
    ]});

    calendar.render();
    });

</script>
@endsection
