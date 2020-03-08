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
                                    eventdate="{{ Carbon\Carbon::parse($event->start)->locale(app()->getLocale())->isoFormat('Do MMM YYYY') }}"
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

            <div class="card-footer">
            <label class="badge badge-success">
                P <i class="fa fa-user"></i>
            </label> : @lang('Present')
            - 
            <label class="badge badge-warning">
                PP <i class="fa fa-clock-o"></i>
            </label> : @lang('Partial presence (arrived late or left early)')
            - 
            <label class="badge badge-info">
                AJ <i class="fa fa-exclamation"></i>
            </label> : @lang('justified absence')
            - 
            <label class="badge badge-danger">
                A <i class="fa fa-user-times"></i>
            </label> : @lang('unjustified absence')
            </div>
            
        </div>
    </div>


</div>

@endsection



@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection
