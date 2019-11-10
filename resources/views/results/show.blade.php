@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        @lang('Course Result Details')
    </h2>
</section>
@endsection


@section('content')

<div class="row">
    
    <div class="col-md-4">
        <div class="card">
                <div class="card-header">
                    <strong>{{ $enrollment->student->firstname }} {{ $enrollment->student->lastname }}</strong>
                </div>    
            <div class="card-body">
                <p>{{ $enrollment->course->name }} ({{ $enrollment->course->period->name }})</p>
            </div>
        </div>
    </div>
    
    

    <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                        @lang('Course result')
                        <!-- TODO ENABLE RESULT EDITION (if the user is allowed to do so) -->
                    <div class="card-header-actions">
                    </div>
                </div>
                
                <div class="card-body">                      
                        <p>
                            {{ $result->result_name->name ?? "-" }}
                        </p>
                </div>
            </div>
        </div>

   @if(isset($result))
        <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                            @lang('Comments')
                    </div>
                    
                    <div class="card-body">                      
                            @forelse ($result->comments as $comment)
                                <p>{{ $comment->body }}</p>
                            @empty
                            <p></p>
                            @endforelse
                    </div>
                </div>
            </div>
    @endif



    
</div>
<div class="row">

@if (count($grades) > 0)
<div class="col-md-6">
        <div class="card">
            <div class="card-header with-border">
                <div class="card-title">
                    @lang('Grades')
            </div>
            
            <div class="card-body">
                <table class="table">
                    @foreach ($grades as $grade)
                    <tr>
                        <td>{{ $grade->grade_type->name }}</td>
                        <td>{{ $grade->grade }} / {{ $grade->grade_type->total }}</td>
                    </tr>
                    @endforeach
                </table>
                <!-- TODO add total -->
            </div>
        </div>
    </div>
    @endif

    @if (count($skills) > 0)
    <div class="col-md-6">
        <div class="card">
            <div class="card-header with-border">
                <div class="card-title">
                    @lang('Skills')
            </div>
            
            <div class="card-body">
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
