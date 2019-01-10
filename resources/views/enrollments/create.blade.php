@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang_u('academico.course_creation')
    </h1>
</section>
@endsection


@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    {{ ucfirst(trans_choice('academico.periods', 1)) }} {{ $period->name }}
                </div>
                <div class="box-tools pull-right">
                    
                    <!-- Period selection dropdown -->
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
            
            <div class="box-body" id="app">           
                
                <table id="coursesTable" class="table table-striped responsive" style="width:100%">
                    <thead>
                        <tr>
                            <th></th>
                            <th>{{ trans_choice('academico.rythms', 1) }}</th>
                            <th>{{ trans_choice('academico.level', 1) }}</th>
                            <th>{{ trans_choice('academico.name', 1) }}</th>
                            <th>{{ trans_choice('academico.volume', 1) }}</th>
                            <th>{{ trans_choice('academico.teacher', 1) }}</th>
                            <th>{{ trans_choice('academico.times', 1) }}</th>
                            <th>{{ trans_choice('academico.enrollments', 1) }}</th>
                            <th>{{ trans_choice('academico.start', 1) }}</th>
                            <th>{{ trans_choice('academico.end', 1) }}</th>
                            <th>{{ trans_choice('academico.actions', 1) }}</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach ($courses as $course)
                        <tr id={{ $course->id }}>

                            <td></td>

                            <td>{{ $course->course_rythm_name }}</td>

                            <td>{{ $course->course_level_name }}</td>

                            <td>{{ $course->name }}</td>

                            <td>{{ $course->volume }}h</td>

                            <td>
                                {{ $course->course_teacher_name }}
                            </td>

                            <td>
                                {{ $course->course_times }}
                            </td>


                            <td>{{ $course->enrollments_count }}</td>

                            <td>{{ $course->start_date }}</td>

                            <td>{{ $course->end_date }}</td>
                            
                            <td><!-- course available actions -->
                                <form action="/enrollments" method="POST">
                                    @csrf
                                    <input type="hidden" name="student_id" value="{{ $student->id }}" />
                                    <input type="hidden" name="course_id" value="{{ $course->id }}" />
                                    <button type="submit" class="btn btn-primary"><i class="fa fa-user-plus"></i></button>
                                </form>
                                
   
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
    <script src="/js/app.js"></script>
@endsection
