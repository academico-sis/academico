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
        schedulerLicenseKey: 'CC-Attribution-NonCommercial-NoDerivatives',
        defaultView: 'timelineWeek',
        resources: <?php echo json_encode($resources) ?>,
        height: "auto",
        minTime: "06:00:00",
        maxTime: "23:00:00",
        nowIndicator: true,
        hiddenDays: [ 0 ],
        firstDay: 1,
        slotWidth: 20,
        resourceAreaWidth: 150,
        eventSources: [
            {
                events: <?php echo json_encode($events) ?>
            },
        ],
        
        eventDrop: function(info) {
            axios.patch(window.location.href, {
                    course_id: info.event.groupId,
                    resource_id: info.newResource.id,
                } )

            .then(function (response) {
                new PNotify({
                    title: "Operation successful",
                    text: "The course has been updated.",
                    type: "success"
                    });
            })
            
            .catch(function (error) {
                console.log(error);
            });
        }
    });

    calendar.render();
});

</script>
    <script src="/js/app.js"></script>
@endsection