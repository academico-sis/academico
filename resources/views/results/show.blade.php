@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Course Result Details')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    
    <div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('Student Info')
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                <p>@lang('name'): {{ $enrollment->student_data->firstname }} {{ $enrollment->student_data->lastname }}</p>
                
                <p>{{ $enrollment->date }}</p>
                        <p>{{ $enrollment->id }}</p>
                        @if ($enrollment->children_count > 0)
                            <p>{{ $enrollment->course_data->name }}</p>
                            <p>@lang('Children enrollments'):</p>
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
                        @lang('Course info')
                    </div>
                    <div class="box-tools pull-right">
                    </div>
                </div>
                
                <div class="box-body">                      
                        <p>
                            {{ $enrollment->result['result_name']['name'] }}
                            @if (true) {{-- if the user has permission to edit result --}}
                            <a href="/result/{{ $enrollment->result['id'] }}/edit" class="btn btn-xs btn-warning">
                                <i class="fa fa-edit"></i>
                            </a>
                            @endif
                        </p>

                        @foreach ($comments as $comment)
                            <p>{{ $comment->body}}</p>
                        @endforeach
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
                    @lang('Grades')
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
                    @lang('Skills')
                </div>
                <div class="box-tools pull-right">
                </div>
            </div>
            
            <div class="box-body">
                    <table class="table">
                            @foreach ($skills as $skill)
                            <tr>
                                <td>{{ $skill->skill->name }}</td>
                                <td>{{ $skill->skill_scale->shortname }}</td>
                            </tr>
                            @endforeach
                        </table>
                
            </div>
        </div>
    </div>
    @endif



</div>

@endsection
