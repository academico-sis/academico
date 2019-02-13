@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Course Schedule')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
        <div class="col-md-12">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        @lang('Some classes need to be updated')
                    </div>
                    
                    <div class="box-tools pull-right">
                        <a href="" class="btn btn-default">@lang('Go back to course list')</a>
                        <button onclick="syncEvents()" class="btn btn-success">@lang('Sync teacher and room (recommended)')</button>
                        <button onclick="syncEventsTeacher()" class="btn btn-primary">@lang('Sync only teacher')</button>
                        <button onclick="syncEventsRoom()" class="btn btn-primary">@lang('Sync only room')</button>

                    </div>
                    
                </div>
                
                <div class="box-body" id="app">

                    @if($outdated_teacher_events->count() > 0)
                    <div class="col-md-6" id="teacherEvents">
                        <p>@lang('The following classes are registered with a different teacher.')</p>                   
                        
                        <ul>
                        @foreach($outdated_teacher_events as $event)
                            <li>{{ $event->start }} @lang('is registered with') {{ $event->teacher->name ?? '-' }}.</li>
                        @endforeach
                        </ul>
                    </div>
                    @else
                    <p>@lang('All events are synced with the new course teacher')</p>
                    @endif

                    @if($outdated_room_events->count() > 0)
                    <div class="col-md-6" id="roomEvents">
                        <p>@lang('The following classes are registered with a different room.')</p>                   
                        
                        <ul>
                        @foreach($outdated_room_events as $event)
                            <li>{{ $event->start }} @lang('is registered in room') {{ $event->room->name ?? '-' }}</li>
                        @endforeach
                        </ul>
                    </div>
                    @else
                    <p>@lang('All events are synced with the new course room')</p>
                    @endif

                </div>
            </div>
        </div>


</div>

@endsection



@section('before_scripts')
    <script>
        
        function syncEvents()
        {
            this.syncEventsRoom();
            this.syncEventsTeacher();
        }

        function syncEventsTeacher()
        {
            var route = {!! json_encode(route('syncEventsTeacher', ['course' => $course->id])) !!};

            axios
                .patch(route)
                .then(function (response) {
                    new PNotify({
                        title: "Operation successful",
                        text: "The events have been updated.",
                        type: "success"
                        });
                    $("#teacherEvents").replaceWith( "<h3>All events are up-to-date with the new course teacher</h3>" );
                })
                .catch(function (error) {
                    console.log(error);
                });
        }

        function syncEventsRoom()
        {
            var route = {!! json_encode(route('syncEventsRoom', ['course' => $course->id])) !!};

            axios
                .patch(route)
                .then(function (response) {
                    new PNotify({
                        title: "Operation successful",
                        text: "The events have been updated.",
                        type: "success"
                        });
                        $("#roomEvents").replaceWith( "<h3>All events are up-to-date with the new course room</h3>" );
                 })
                .catch(function (error) {
                    console.log(error);
                });
        }

    </script>
@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection