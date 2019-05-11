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
    
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('Classes without attendance')
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                <table class="table">
                    @foreach($pending_attendance as $event)
                    <tr>
                        <td>{{ $event['event'] }}</td>
                        <td>{{ $event['event_date'] }}</td>
                        <td>{{ $event['teacher'] }}</td>
                        <td>{{ $event['pending'] }}</td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('Unjustified Absences')
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>@lang('Student')</th>
                            <th>@lang('Course')</th>
                            <th>@lang('Number of absences')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($unjustified_absences as $absence)
                            @if($absence->absence_count > 0)
                            <tr>
                                <td><a href="{{ route('studentAttendance', ['student' => $absence->student_id]) }}">{{ $absence->firstname }} {{ $absence->lastname }}</a></td>
                                <td>{{ $absence->course_name }}</td>
                                <td>{{ $absence->absence_count }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('Justified Absences')
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>@lang('Student')</th>
                            <th>@lang('Course')</th>
                            <th>@lang('Number of absences')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($justified_absences as $absence)
                            @if($absence->absence_count > 0)
                            <tr>
                                <td><a href="{{ route('studentAttendance', ['student' => $absence->student_id]) }}">{{ $absence->firstname }} {{ $absence->lastname }}</a></td>
                                <td>{{ $absence->course_name }}</td>
                                <td>{{ $absence->absence_count }}</td>
                            </tr>
                            @endif
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('after_scripts')
@endsection
