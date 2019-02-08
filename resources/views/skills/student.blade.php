@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Edit Student Skills')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('Edit Student Skills')
                </div>

                <div class="box-tools pull-right">
                </div>  
            </div>
            
            <div class="box-body" id="app">    
                <student-skills-component
                    :saved_skills="{{ json_encode($skills) }}"
                    :student="{{ json_encode($student) }}"
                    :skill_scales="{{ json_encode($skillScales) }}"
                    :course="{{ json_encode($course) }}"
                    route="{{ route('storeSkillEvaluation') }}">
                </student-skills-component>

                <course-result-component
                    :student="{{ json_encode($student) }}"
                    :course="{{ json_encode($course) }}"
                    :results="{{ json_encode($results) }}"
                    :comments="{{ json_encode($course_result->comments ?? null) }}"
                    :course_result="{{ json_encode($course_result) }}">
                </course-result-component>

            </div>
        </div>
    </form>
    </div>
</div>

@endsection


@section('before_scripts')

@endsection


@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection
