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
    {{ $current_period }} <span class="caret"></span>
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
                        <th>Modalidad</th>
                        <th>Nivel</th>
                        <th>Description</th>
                        <th>Volume</th>
                        <th>Profesor</th>
                        <th>Aula</th>
                        <th>Horario</th>
                        <th>Inscritos</th>
                        <th>Inicia</th>
                        <th>Finaliza</th>
                        <th>Acciones</th>
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
