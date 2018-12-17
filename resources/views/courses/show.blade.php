@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang_u('academico.coursedetails')
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
                    
                    
                </div>
                
            </div>
            
            <div class="box-body">           
                
                <table id="DataTables_Table_0" class="table table-striped table-bordered responsive">
                    <thead>
                        <tr>
                            <th>@lang_u('academico.students')</th>
                            <th>@lang_u('academico.age')</th>
                            <th>@lang_u('academico.birthdate')</th>
                            <th>@lang_u('academico.email')</th>
                            <th>@lang_u('academico.actions')</th>
                        </tr>
                    </thead>
                    
                    <tbody>
                        @foreach ($students as $student)
                        <tr>
                            <td>{{ $student->student_name }}</td>
                            <td>{{ $student->student_age }}</td>
                            <td>{{ $student->student_birthdate }}</td>
                            <td>{{ $student->student_email }}</td>

                            <td><!-- available actions -->

   
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
