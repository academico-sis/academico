@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang_u('academico.pending')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                        @lang_u('academico.pending')
                </div>
                <div class="box-tools pull-right">
                    
                </div>
                
            </div>
            
            <div class="box-body">           
                
                <table class="table table-striped responsive">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>@lang_u('academico.firstname')</th>
                            <th>@lang_u('academico.lastname')</th>
                            <th>@lang_u('academico.course_name')</th>
                            <th>@lang_u('academico.period')</th>
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
                            <td>
                                <a href="/enrollments" class="btn btn-primary">
                                    <i class="fa fa-dollar"></i>
                                </a>

                                <a href="" class="btn btn-info">
                                        <i class="fa fa-eye"></i>
                                    </a>
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
