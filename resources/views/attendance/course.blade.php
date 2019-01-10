@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang_u('academico.attendance')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang_u('academico.attendance')
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                <div id="app">
                <table class="table">
                        <thead>
                        @foreach ($course->events as $event)
                        <td></td>
                            <td><a href="/attendance/event/{{ $event->id }}">{{ $event->start }}</a></td>
                        @endforeach
                    </thead>

                        @foreach ($attendances as $attendance)
                        <student-attendance-component
                            :student="{{ json_encode($attendance) }}"></student-attendance-component>
                        @endforeach
                        
                </table>
            </div>
            </div>
        </div>
    </div>


</div>

@endsection



@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection
