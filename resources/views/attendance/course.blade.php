@extends('backpack::blank')


@section('content')

<div class="row">

    <div class="col-md-12">
    @include('partials.course_info')

@if ($course->exempt_attendance == 1)
    <div class="alert alert-warning" role="alert">La prise de présence est désactivée pour ce cours. Les fiches de présences en retard ne s'afficheront pas dans les tableaux de bord.</div>
@endif

        <div class="card">
                    <div class="card-header">@lang('Attendance')</div>
            <div class="card-body">
                <div id="app">
                <table class="table">
                    <thead>
                        <td></td>
                        @foreach ($events as $event)
                            <td>
                                <event-attendance-status-component
                                    :exempted="{{ $event->exempt_attendance }}"
                                    toggleroute="{{ route('toggleEventAttendance', ['event' => $event->id ]) }}"
                                    eventattendanceroute="{{ route('eventAttendance', ['event' => $event->id]) }}"
                                    eventdate="{{ Carbon\Carbon::parse($event->start)->toFormattedDateString() }}"
                                    :isadmin="{{ json_encode($isadmin) }}"
                                ></event-attendance-status-component>
                            </td>
                        @endforeach
                    </thead>

                    <tbody>
                        @foreach ($attendances as $a => $attendance)
                            <tr is="course-attendance-component"
                                :student="{{ json_encode($attendance) }}"
                                studentdetailsroute="{{ route('student.show', ['id' => $a]) }}"
                            >
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
