@extends('backpack::layout')

@section('header')
    <section class="content-header">
      <h1>
        {{ trans('backpack::base.dashboard') }}
      </h1>
      <ol class="breadcrumb">
        <li><a href="{{ backpack_url() }}">{{ config('backpack.base.project_name') }}</a></li>
        <li class="active">{{ trans('backpack::base.dashboard') }}</li>
      </ol>
    </section>
@endsection


@section('content')
    <div class="row">

        @if(isset($pending_attendance))
        <div class="col-md-4">
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
                                <li>{{ $event->start }}: <a href="/attendance/event/{{ $event->id }}">{{ $event->name }}</a>
                            </li>
                            @endforeach
                        </ul>
    
                    </div>
                </div>
            </div>
        @endif


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

                        @if($course->events->count() > 0 && $course->exempt_attendance !== 1)
                            <a href="/attendance/course/{{$course->id}}" class="btn btn-default btn-xs"><i class="fa fa-calendar"></i></a>
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
@endsection
