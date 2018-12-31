@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        {{ ucfirst(trans_choice('academico.pending', 2)) }}
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                        {{ ucfirst(trans_choice('academico.pending', 2)) }}
                </div>
                <div class="box-tools pull-right">
                    
                </div>
                
            </div>
            
            <div class="box-body">           
                
                <table class="table table-striped responsive">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>{{ ucfirst(trans_choice('academico.firstname', 1)) }}</th>
                            <th>{{ ucfirst(trans_choice('academico.lastname', 1)) }}</th>
                            <th>{{ ucfirst(trans_choice('academico.course_name', 1)) }}</th>
                            <th>{{ ucfirst(trans_choice('academico.period', 1)) }}</th>
                            <th>Actions</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($enrollments as $enrollment)
                        <tr>
                            <td>{{ $enrollment->id }}</td>
                            <td>{{ $enrollment->student_data->student_first_name }}</td>
                            <td>{{ $enrollment->student_data->student_last_name }}</td>
                            <td>{{ $enrollment->course_data->name }}</td>
                            <td>{{ $enrollment->course_data->period->name }}</td>
                            <td></td>
                        </tr>
                        @endforeach
                    </tbody>
                    
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
