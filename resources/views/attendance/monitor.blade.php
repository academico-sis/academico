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
                <div class="row">
                    <div class="col-sm-5">
                        <h4 class="card-title mb-0">@lang('Courses')</h4>
                    </div>
                    <div class="col-sm-7 d-none d-md-block">
                        <div class="card-header-actions">
                            <!-- Period selection dropdown -->
                            @include('partials.period_selection')
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <table class="table">
                <thead>
                    <tr>
                        <th>@lang('Course')</th>
                        <th>@lang('Teacher')</th>
                        <th>@lang('Missing attendance')</th>
                        <th>@lang('Attendance Status')</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($courses as $c => $course)
                    <tr is="course-attendance-status-component"
                        :count="{{ $courses[$c]['missing'] ?? 0 }}"
                        :exempted="{{ $courses[$c]['exempt_attendance'] ?? 0 }}"
                        toggleroute="{{ route('toggleCourseAttendance', ['course' => $courses[$c]['id'] ]) }}"
                        coursename="{{ $courses[$c]['name'] }}"
                        teachername="{{ $courses[$c]['teachername'] }}"
                        courseattendanceroute="{{ route('monitorCourseAttendance', ['course' => $courses[$c]['id'] ]) }}"
                        :isadmin="{{ json_encode($isadmin) }}"
                        ></tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-sm-5">
                        <h4 class="card-title mb-0">@lang('Student Attendance Overview')</h4>
                    </div>
                    <div class="col-sm-7 d-none d-md-block">
                        <div class="card-header-actions">
                            <!-- Period selection dropdown -->
                            @include('partials.period_selection')
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="card-body">
                <table class="table">
                    <thead>
                        <tr>
                            <th>@lang('Student')</th>
                            <th>@lang('Course')</th>
                            <th>@lang('Number of non-justified absences')</th>
                            <th>@lang('Number of justified absences')</th>
                            <th>@lang('Total')</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absences as $absence)
                            @if($absence->unjustified_absence_count > 0 || $absence->justified_absence_count > 0 )
                            <tr>
                                <td><a href="{{ route('studentAttendance', ['student' => $absence->student_id]) }}?course_id={{ $absence->course_id }}">{{ $absence->firstname }} {{ $absence->lastname }}</a></td>
                                <td>{{ $absence->course_name }}</td>
                                <td>{{ $absence->unjustified_absence_count }}</td>
                                <td>{{ $absence->justified_absence_count }}</td>
                                <td>
                                    @if ($absence->total_absence_count > 3)
                                        <span class="badge badge-pill badge-danger">{{ $absence->total_absence_count }}</span>
                                    @elseif ($absence->total_absence_count > 1)
                                        <span class="badge badge-pill badge-warning">{{ $absence->total_absence_count }}</span>
                                    @else
                                        <span class="badge badge-pill badge-info">{{ $absence->total_absence_count }}</span>
                                    @endif
                                </td>
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
