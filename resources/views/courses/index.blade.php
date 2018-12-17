@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('academico.courses')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('academico.courses')
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
                            <th>@lang_u('academico.rythm')</th>
                            <th>@lang_u('academico.level')</th>
                            <th>@lang_u('academico.name')</th>
                            <th>@lang_u('academico.volume')</th>
                            <th>@lang_u('academico.teacher')</th>
                            <th>@lang_u('academico.room')</th>
                            <th>@lang_u('academico.times')</th>
                            <th>@lang_u('academico.enrollments')</th>
                            <th>@lang_u('academico.start')</th>
                            <th>@lang_u('academico.end')</th>
                            <th>@lang_u('academico.actions')</th>
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
                            <td>Inicia</td>
                            <td>Finaliza</td>
                            <td>Acciones</td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
