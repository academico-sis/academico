@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('View Skills for Group')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">

        @if($skills->count() > 0)
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('View Skills for Group')
                </div>

                <div class="box-tools pull-right">
                </div>  
            </div>
            
            <div class="box-body" id="app">
                    <div class="table-responsive" style="overflow: auto">
                        <table class="table">
                            <tr>
                                <td></td>
                                    @foreach ($skills as $skill)
                                        <td title="{{ $skill->name }}">{{ substr($skill->name, 0, 30) }}...</td>
                                    @endforeach
                            </tr>

                                @foreach ($enrollments as $enrollment)
                                    <tr>
                                        <td>
                                            <a href="{{ route('studentSkillsEvaluation', ['course' => $course->id, 'student' => $enrollment->student->id]) }}">
                                                {{ $enrollment->student->user->firstname }} {{ $enrollment->student->user->lastname }}
                                            </a>
                                        </td>

                                        @foreach ($skills as $skill)
                                            <td class="{{ $skill_evaluations->where('student_id', $enrollment->student->id)->where('skill_id', $skill->id)->first()['skill_scale']['style'] }}">
                                                {{ $skill_evaluations->where('student_id', $enrollment->student->id)->where('skill_id', $skill->id)->first()['skill_scale']['shortname'] }}
                                            </td>
                                        @endforeach
                                    </tr>
                                @endforeach
                        </table>
                    </div>
            </div>
        </div>
        @else
            <p>@lang('This course has no skills yet')</p>
        @endif
    </div>
</div>

@endsection


@section('before_scripts')
   
@endsection


@section('after_scripts')
<script src="/js/app.js"></script>

@endsection
