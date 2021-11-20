@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        @lang('Edit Student Skills')
    </h2>
</section>
@endsection


@section('content')

<div class="row">
    <student-skills-component
        :saved_skills="{{ json_encode($skills) }}"
        :enrollment="{{ json_encode($enrollment) }}"
        :skill_scales="{{ json_encode($skillScales) }}"
        route="{{ route('storeSkillEvaluation') }}">
    </student-skills-component>

    <div class="col-md-6">
    <course-result-component
    result-post-route="{{ route('storeResult') }}"
    :enrollment="{{ json_encode($enrollment) }}"
    :results="{{ json_encode($results) }}"
    :result="{{ json_encode($result) }}"
    writeaccess="{{ $writeaccess }}">
</course-result-component>

    @if (isset($result))
    <student-comments
        route="{{ route('storeComment') }}"
        :comments="{{ json_encode($result->comments ?? null) }}"
        :id="{{ json_encode($result->id) }}"
        :type="'App\\Models\\Result'"
        >
    </student-comments>
    @endif
    </div>

</div>

@endsection


@section('before_scripts')

@endsection