@extends('backpack::blank')

@section('header')
<section class="container-fluid">
	  <h2>
        @lang('Attendance')
    </h2>
</section>
@endsection


@section('content')

<div class="row" id="app">
    
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                @lang('Courses')
            </div>
            
            <div class="card-body">
                <table class="table">
                <thead>
                    <tr>
                        <th>@lang('Course')</th>
                        <th>@lang('Teacher')</th>
                        <th>@lang('Attendance')</th>
                    </tr>
                    </thead>
                    @foreach($courses as $course)
                    <tr>
                        <td><a href="{{ route('monitorCourseAttendance', ['course' => $course->id ]) }}">{{ $course->name }}</a></td>
                        <td>{{ $course->course_teacher_name }}</td>
                        <td>
                            <course-attendance-status-component
                                :count="{{ $course->missing_attendance }}"
                                :exempted="{{ $course->exempt_attendance }}"
                                toggleroute="{{ route('toggleCourseAttendance', ['course' => $course->id ]) }}"></course-attendance-status-component>
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
                                <td><a href="student/{{$absence->student_id}}/show">{{ $absence->firstname }} {{ $absence->lastname }}</a></td>
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
<script src="/js/app.js"></script>

@endsection
