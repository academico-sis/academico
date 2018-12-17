@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang_u('academico.courses')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang_u('academico.courses')
                </div>
                <div class="box-tools pull-right">
                    
                    <!-- Period selection dropdown -->
                    <div class="btn-group">
                        <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                            {{ $period->name }} <span class="caret"></span>
                        </button>
                        <ul class="dropdown-menu">
                            @foreach ($periods as $period)
                            <li><a href="/courses/{{ $period->id }}">{{ $period->name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                    
                </div>
                
            </div>
            
            <div class="box-body">           
                
                <table id="DataTables_Table_0" class="table table-striped table-bordered responsive">
                    <thead>
                        <tr>
                            <th>{{ trans_choice('academico.rythms', 1) }}</th>
                            <th>{{ trans_choice('academico.level', 1) }}</th>
                            <th>{{ trans_choice('academico.name', 1) }}</th>
                            <th>{{ trans_choice('academico.volume', 1) }}</th>
                            <th>{{ trans_choice('academico.teacher', 1) }}</th>
                            <th>{{ trans_choice('academico.room', 1) }}</th>
                            <th>{{ trans_choice('academico.times', 1) }}</th>
                            <th>{{ trans_choice('academico.enrollments', 1) }}</th>
                            <th>{{ trans_choice('academico.start', 1) }}</th>
                            <th>{{ trans_choice('academico.end', 1) }}</th>
                            <th>{{ trans_choice('academico.actions', 1) }}</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach ($courses as $course)
                        <tr>
                            <td>{{ $course->course_rythm_name }}</td>
                            <td>{{ $course->course_level_name }}</td>
                            <td>{{ $course->name }}</td>
                            <td>{{ $course->volume }}h</td>
                            <td>{{ $course->course_teacher_name }}</td>
                            <td>{{ $course->course_room_name }}</td>
                            <td>{{ $course->course_times }}</td>
                            <td>{{ $course->enrollments_count }}</td>
                            <td>{{ $course->start_date }}</td>
                            <td>{{ $course->end_date }}</td>
                            
                            <td><!-- course available actions -->

                            <!-- list of students -->
                            @if ($course->enrollments_count > 0)
                            <a type="button" class="btn btn-xs btn-secondary" href="course/{{ $course->id }}">
                                <i class="fa fa-user"></i>
                            </a>
                            @endif

                            <!-- attendance overview -->
                            @if ($course->exempt_attendance !== 1)
                            <a type="button" class="btn btn-xs btn-secondary" href="attendance/course/{{ $course->id }}">
                                <i class="fa calendar-check-o"></i>
                            </a>
                            @endif

                            <!-- todo course times editing -->

                            <!-- todo course teacher and room editing -->

                            <!-- todo grades or skills editing -->

                            <!-- course deletion -->
                            @if ($course->enrollments_count == 0) <!-- todo restrict to admins -->
                            <a type="button" class="btn btn-xs btn-danger" href="courses/{{ $course->id }}/delete">
                                <i class="fa fa-trash"></i>
                            </a>
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
