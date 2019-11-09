@extends('backpack::blank')

@section('header')
<section class="container-fluid">
	  <h2>
        @lang('Attendance')
    </h2>
</section>
@endsection


@section('content')

<div class="row">
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                @lang('Classes without attendance')
            </div>
            
            <div class="card-body">
                <table class="table">
                <thead>
                    <tr>
                        <th>@lang('Course')</th>
                        <th>@lang('Date')</th>
                        <th>@lang('Teacher')</th>
                        <th>@lang('Missing students')</th>
                        <th>@lang('Actions')</th>
                    </tr>
                    </thead>
                    @foreach($pending_attendance as $event)
                    <tr>
                        <td>{{ $event['event'] }}</td>
                        <td>{{ $event['event_date'] }}</td>
                        <td>{{ $event['teacher'] }}</td>
                        <td>{{ $event['pending'] }}</td>
                        <td>
                            <a class="btn btn-primary btn-sm" href="{{ route('eventAttendance', ['event' => $event['event_id']]) }}" title="@lang('View attendance sheet for event')"><i class="fa fa-eye"></i></a>
                            <a class="btn btn-warning btn-sm" href="{{ route('exemptEventAttendance', ['event' => $event['event_id']]) }}" title="@lang('Exempt event from attendance sheet')" onclick="return confirm('Are you sure? The attendance sheet for this event will be deleted')"><i class="fa fa-times"></i></a>
                            <a class="btn btn-warning btn-sm" href="{{ route('exemptCourseAttendance', ['course' => $event['course_id']]) }}" title="@lang('Exempt all course events from attendance sheet')" onclick="return confirm('Are you sure? The attendance sheet for all course events will be deleted')"><i class="fa fa-times"></i><i class="fa fa-times"></i></a>

                        </td>
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                @lang('Unjustified Absences')
            </div>
            
            <div class="card-body">
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

        <div class="card">
            <div class="card-header">
                @lang('Justified Absences')
            </div>
            
            <div class="card-body">
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
                                <td><a href="student/{{$absence->student_id}}">{{ $absence->firstname }} {{ $absence->lastname }}</a></td>
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
