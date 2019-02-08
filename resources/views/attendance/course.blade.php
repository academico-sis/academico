@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Attendance')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('Attendance')
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                <div id="app">
                <table class="table">
                        <thead>
                            <td></td>
                        @foreach ($events as $event)
                            <td><a href="{{ route('eventAttendance', ['event' => $event->id]) }}">
                                {{ Carbon\Carbon::parse($event->start)->toFormattedDateString() }}
                            </a></td>
                        @endforeach
                    </thead>

                    <tbody>
                        @foreach ($attendances as $attendance)
                            <tr is="course-attendance-component"
                                :student="{{ json_encode($attendance) }}">
                            </tr>
                        @endforeach
                    </tbody>
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
