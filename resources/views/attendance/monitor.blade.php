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
                        <h4 class="card-title mb-0">@lang('Attendance Monitor')</h4>
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
                <table class="table" id="studentAttendanceTable">
                    <thead>
                        <tr>
                            <th>@lang('Student')</th>
                            <th>@lang('Course')</th>
                            <th>@lang('Teacher')</th>
                            <th>@lang('Total')</th>

                        </tr>
                    </thead>
                    <tbody>
                        @foreach($absences as $absence)
                            <tr>
                                <td><a href="{{ route('studentAttendance', ['student' => $absence->first()->student_id]) }}?course_id={{ $absence->first()->event->course_id }}">{{ $absence->first()->student_name }}</a></td>
                                <td>{{ $absence->first()->event->course->name }}</td>
                                <td>{{ $absence->first()->event->course->course_teacher_name }}</td>
                                <td>
                                    @if ($absence->count() > 3)
                                        <span class="badge badge-pill badge-danger">{{ $absence->count() }}</span>
                                    @elseif ($absence->count() > 1)
                                        <span class="badge badge-pill badge-warning">{{ $absence->count() }}</span>
                                    @else
                                        <span class="badge badge-pill badge-info">{{ $absence->count() }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

    </div>
</div>

@endsection

@section('after_scripts')
    <script src="{{ mix('/js/app.js') }}"></script>
    <script src="{{ mix('/js/manifest.js') }}"></script>
    <script src="{{ mix('/js/vendor.js') }}"></script>

    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/datatables.min.css"/>

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/datatables.min.js"></script>
<script>
    $(document).ready(() =>
        $('#studentAttendanceTable').DataTable({
            "paging": false,
            "order": [[3, "desc"]]
        }));
</script>
@endsection
