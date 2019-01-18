@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('pending')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                        @lang('pending')
                </div>
                <div class="box-tools pull-right">
                    
                </div>
                
            </div>
            
            <div class="box-body">           
                
                <table class="table table-striped responsive">
                    <thead>
                        <tr>
                            <th>@lang('ID')</th>
                            <th>@lang('firstname')</th>
                            <th>@lang('lastname')</th>
                            <th>@langCourse Name</th>
                            <th>@lang('period')</th>
                            <th>@lang('Actions')</th>
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
                                <a href="/student/enroll" class="btn btn-primary">
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
