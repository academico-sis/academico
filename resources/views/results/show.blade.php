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
                                <strong>{{ $enrollment->student->firstname }} {{ $enrollment->student->lastname }}</strong>
                        </div>
                </div>    
            <div class="box-body">
                
                <p>{{ $enrollment->course->name }} ({{ $enrollment->course->period->name }})</p>
            </div>
        </div>
    </div>
    
    

    <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        @lang('Course result')
                    </div>
                    <div class="box-tools pull-right">
                        {{--  todo enable result edition                           @if (true) {{-- if the user has permission to edit result 
                            <a href="/result/{{ $enrollment->result['id'] }}/edit" class="btn btn-xs btn-warning">
                                <i class="fa fa-edit"></i>
                            </a>
                            @endif --}}
                    </div>
                </div>
                
                <div class="box-body">                      
                        <p>
                            {{ $result->result_name->name }}
                        </p>
                </div>
            </div>
        </div>

   
        <div class="col-md-4">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            @lang('Comments')
                        </div>
                        <div class="box-tools pull-right">
                            {{--  todo enable result edition                           @if (true) {{-- if the user has permission to edit result 
                                <a href="/result/{{ $enrollment->result['id'] }}/edit" class="btn btn-xs btn-warning">
                                    <i class="fa fa-edit"></i>
                                </a>
                                @endif --}}
                        </div>
                    </div>
                    
                    <div class="box-body">                      
                            @foreach ($result->comments as $comment)
                                <p>{{ $comment->body }}</p>
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
                                @if($skill->skill_scale->id == 1)
                                    <td class="bg-danger">{{ $skill->skill_scale->shortname }}</td>
                                @elseif ($skill->skill_scale->id == 2)
                                    <td class="bg-warning">{{ $skill->skill_scale->shortname }}</td>
                                @elseif ($skill->skill_scale->id == 3)
                                    <td class="bg-success">{{ $skill->skill_scale->shortname }}</td>
                                @else
                                    <td>{{ $skill->skill_scale }}</td>
                                @endif
                            </tr>
                            @endforeach
                        </table>
                
            </div>
        </div>
    </div>
    @endif



</div>

@endsection
