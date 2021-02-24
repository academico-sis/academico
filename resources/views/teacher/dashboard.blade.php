@extends('backpack::blank')

@section('content')
    <div class="row h-100 justify-content-center align-items-center">

        <div class="col-md-8">

            <h2 class="text-center">Bonjour {{ $teacher->firstName }}</h2>

        </div>

        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    <strong>
                        @lang('Selected Period')
                    </strong>
                </div>

                <div class="card-body p-3 d-flex align-items-center"><i class="la la-calendar bg-primary p-3 font-2xl mr-3"></i>
                    <div>
                        @include('partials.period_selection')
                    </div>
                </div>
            </div>
        </div>
    </div><!-- /.row -->

    <div class="row">

        <div class="col-md-8">

            <div class="row">
                @foreach ($courses as $course)
                    <div class="col-md-6">
                        <div class="card">
                            <div class="card-header">
                                <strong>{{ $course->name }}</strong>
                                <div class="card-header-actions">
                                    @if($course->campus_id == 1)
                                        <a href="/course/{{$course->id}}/show" class="btn btn-default btn-xs"><i class="la la-users"></i></a>
                                    @endif

                                    @if($course->enrollments->count() > 0 && $course->events->count() > 0 && $course->exempt_attendance !== 1)
                                        <a href="{{ route('monitorCourseAttendance', ['course' => $course->id]) }}" class="btn btn-default btn-xs"><i class="la la-calendar"></i></a>
                                    @endif

                                    @if($course->evaluationType && $course->evaluationType->skills->count() > 0 && $course->course_enrollments_count > 0)
                                        <a href="{{ route('courseSkillsEvaluation', ['course' => $course->id]) }}" class="btn btn-xs btn-default">
                                            <i class="la la-th"></i>  {{ __('Evaluate skills') }}
                                        </a>
                                    @endif

                                    @if($course->evaluationType && $course->evaluationType->gradeTypes->count() > 0 && $course->course_enrollments_count > 0)
                                        <a href="/course/{{$course->id}}/grades" class="btn btn-xs btn-default">
                                            <i class="la la-percent"></i> {{ __('Manage grades') }}
                                        </a>
                                    @endif

                                </div>
                            </div>

                            <div class="card-body">
                                <p>
                                    <i class="la la-users"></i> {{ $course->enrollments_count }} @lang('students')<br>
                                    <i class="la la-calendar"></i> {{ $course->start_date->toFormattedDateString() }} - {{ $course->end_date->toFormattedDateString() }}<br>
                                    <i class="la la-clock-o"></i> {{ $course->course_times }}<br>
                                    @if ($course->room)<i class="la la-home"></i>{{ $course->room->name }}<br>@endif
                                    <i class="la la-bar-chart"></i> {{ $course->volume }} @lang('hours') + {{ $course->remote_volume }} @lang('hours')
                                </p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div><!-- /.row -->
        </div>

        <div class="col-md-4">
            @if(isset($pending_attendance) && $pending_attendance->count() > 0)
                <div class="card">
                    <div class="card-header">
                        <strong>
                            @lang('Pending Attendance')
                        </strong>
                    </div>

                    <div class="card-body">
                        <ul>
                            @foreach($pending_attendance as $event)
                                <li>{{ $event->start }}: <a href="{{ route('eventAttendance', ['event' => $event->id]) }}">{{ $event->name }}</a>
                                </li>
                            @endforeach
                        </ul>

                    </div>
                </div>
            @endif
        </div>
    </div><!-- /.row -->
@endsection
