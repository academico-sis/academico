@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang_u('academico.grades_edit')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang_u('academico.grades_edit')
                </div>
                
                <div class="box-tools pull-right">
                    
                </div>  
            </div>
            
            <div class="box-body">           
                <table class="table">
                    
                    @foreach ($enrollments as $enrollment)
                    <tr>
                        <td>{{ $enrollment->student_data->firstname }}</td>
                        <td>
                            <ul>
                                @foreach ($enrollment->grades as $grade)
                                <li>{{ $grade->grade_type->name }} : {{ $grade->grade }} / {{ $grade->grade_type->total }} </li>
                                @endforeach
                            </ul>
                        </td>
                        
                        {{-- for each grade type associated to the course --}}
                        {{-- retrieve  --}}
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('after_scripts')

@endsection
