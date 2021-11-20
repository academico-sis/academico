@extends('backpack::blank')


@section('content')

<div class="row">

    <div class="col-lg-8 col-lg-offset-2">
        <div class="card">
            <div class="card-header">
                Présences pour la classe du {{ Carbon\Carbon::parse($event->start)->locale(app()->getLocale())->isoFormat('Do MMM YYYY') }}
                <div class="card-header-actions">
                    <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">@lang('Enroll new student')</button>
                    <a href="/attendance/course/{{ $event->course_id }}">@lang('Back to course')</a>
                </div>
            </div><!-- /.card-header -->

            <div class="card-body">
                <table class="table table-striped">
                    <thead>
                        <td>Student</td>
                        <td></td>
                    </thead>

                    <tbody>
                        @foreach ($attendances as $attendance)
                            <tr is="event-attendance-component"
                                :attendance="{{ json_encode($attendance) }}"
                                :attendance_types="{{ json_encode($attendance_types) }}"
                                :event="{{ json_encode($event) }}"
                                route="{{ route('storeAttendance') }}">
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

@include('partials.enrollment_modal', ['course_id' => $event->course->parent])

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

@endsection
