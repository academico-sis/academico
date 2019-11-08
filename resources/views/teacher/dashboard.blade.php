@extends('backpack::blank')

@section('header')
    <section class="container-fluid">
	  <h2>
        {{ trans('backpack::base.dashboard') }}
      </h2>
    </section>
@endsection


@section('content')
    <div class="row">


        <div class="col-md-4">
                <div class="info-box">
                        <span class="info-box-icon bg-black"><i class="fa fa-calendar"></i></span>
                        <div class="info-box-content">
                            <span class="info-box-text">@lang('Current Period')</span>
                            <div class="btn-group">
                                    <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        {{ $period->name }} <span class="caret"></span>
                                    </button>
                                    <ul class="dropdown-menu">
                                        @foreach ($periods as $period)
                                        <li><a href="{{ url()->current() }}/?period={{ $period->id }}">{{ $period->name }}</a></li>
                                        @endforeach
                                    </ul>
                                </div>
                        </div>
                    </div>

        @if(isset($pending_attendance) && $pending_attendance->count() > 0)
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            <strong>
                                @lang('Pending Attendance')
                            </strong>
                        </div>
                        <div class="box-tools pull-right">
                        
                        </div>
                    </div>
    
                    <div class="box-body">
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

        <div class="col-md-8">

        @foreach ($courses as $course)
        <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">                          
                        <strong>{{ $course->name }}</strong>
                    </div>
                    <div class="box-tools pull-right">
                        @if($course->campus_id == 1)
                            <a href="/course/{{$course->id}}" class="btn btn-default btn-xs"><i class="fa fa-users"></i></a>
                        @endif

                        @if($course->enrollments->count() > 0 && $course->events->count() > 0 && $course->exempt_attendance !== 1)
                            <a href="{{ route('monitorCourseAttendance', ['course' => $course->id]) }}" class="btn btn-default btn-xs"><i class="fa fa-calendar"></i></a>
                        @endif

                        @if($course->evaluation_type->contains(1))
                            <a href="/course/{{$course->id}}/grades" class="btn btn-xs btn-default">
                                <i class="fa fa-percent"></i>
                            </a>
                        @endif

                        @if($course->evaluation_type->contains(2))
                            <a href="{{ route('courseSkillsEvaluation', ['course' => $course->id]) }}" class="btn btn-xs btn-default">
                                <i class="fa fa-th"></i>
                            </a>
                        @endif

                    </div>
                </div>

                <div class="box-body">
                    <p>
                        {{ $course->enrollments_count }} @lang('students')<br>
                        {{ $course->start_date }} - {{ $course->end_date }}<br>
                        {{ $course->course_times }}<br>
                        {{ $course->volume }} @lang('hours')
                    </p>

                    <p>
                        
                    </p>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    </div>
@endsection
