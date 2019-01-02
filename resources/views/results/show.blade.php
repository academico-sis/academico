@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        {{ ucfirst(trans_choice('academico.enrollment_details', 1)) }}
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    
    <div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    {{ ucfirst(trans_choice('academico.student_info', 1)) }}
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                <p>{{ ucfirst(trans_choice('academico.name', 1)) }}: {{ $enrollment->student_data->firstname }} {{ $enrollment->student_data->lastname }}</p>
                
                <p>{{ $enrollment->date }}</p>
                        <p>{{ $enrollment->id }}</p>
                        @if ($enrollment->children_count > 0)
                            <p>{{ $enrollment->course_data->name }}</p>
                            <p>Children enrollments:</p>
                            <ul>
                            @foreach ($enrollment->children as $children)
                                <li><a href="/enrollments/{{ $children->id }}">{{ $children->course_data->name }}</a>
                                    ({{ $children->course_data->period->name }})</li>
                            @endforeach
                            </ul>
                        @endif
                        <p>{{ $enrollment->course_data->period->name }}</p>
            </div>
        </div>
    </div>
    
    

    <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        {{ ucfirst(trans_choice('academico.course_info', 1)) }}
                    </div>
                    <div class="box-tools pull-right">
                    </div>
                </div>
                
                <div class="box-body">                      
                        <p>
                            {{ $enrollment->result['result_name']['name'] }}
                            <a href="/results/{{ $enrollment->id }}" class="btn btn-xs btn-info">
                                <i class="fa fa-eye"></i>
                            </a>
                        </p>

                        Comments
                </div>
            </div>
        </div>

   


    
</div>
<div class="row">

    @if (count($grades) > 0)
<div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    {{ ucfirst(trans_choice('academico.grades', 2)) }}
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                <table class="table">
                    @foreach ($grades as $grade)
                    <tr>
                        <td>{{ $grade->grade_type->name }}</td>
                        <td>{{ $grade->grade }} / {{ $grade->grade_type->total }}</td>
                    </tr>
                    @endforeach
                </table>
                {{-- todo add total --}}
            </div>
        </div>
    </div>
    @endif

    @if (count($skills) > 0)
    <div class="col-md-6">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    {{ ucfirst(trans_choice('academico.skills', 2)) }}
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                
                
            </div>
        </div>
    </div>
    @endif



</div>

@endsection
