@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        {{ ucfirst(trans_choice('academico.attendance', 1)) }}
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    {{ ucfirst(trans_choice('academico.attendance', 1)) }}
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                <table class="table">
                    <thead>
                        @foreach ($course->events as $event)
                        <td></td>
                            <td>{{ $event->id }}</td>
                        @endforeach

                    </thead>

                    <tbody>
                        @foreach ($course->enrollments as $student)
                            <tr>
                                <td>
                                    {{ $student->student_data->firstname }}
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
