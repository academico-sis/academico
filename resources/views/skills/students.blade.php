@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        @lang('View Skills for Group')
    </h2>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">

        @if($skills->count() > 0)
        <div class="card">
            <div class="card-header">
                @lang('View Skills for Group')
            </div>

            <div class="card-body" id="app">
                    <div class="table-responsive" style="overflow: auto">
                        <table class="table">
                            <tr>
                                <td></td>
                                    @foreach ($skills as $category)
                                        <td> {{ $category[0]->skill_type->name }}</td>
                                    @endforeach
                            </tr>

                            @foreach ($enrollments as $enrollment)
                                <tr>
                                    <td>
                                        <a href="{{ route('studentSkillsEvaluation', ['course' => $course->id, 'student' => $enrollment->student->id]) }}">
                                            {{ $enrollment->student->user->firstname }} {{ $enrollment->student->user->lastname }}
                                        </a>
                                    </td>
                                    @foreach ($skills as $category)
                                        <td>
                                            <span class="badge badge-danger">{{ $enrollment->skill_evaluations->where('skill.skill_type_id', $category[0]->skill_type_id)->where('skill_scale_id', 1)->count() }}</span> -
                                            <span class="badge badge-warning">{{ $enrollment->skill_evaluations->where('skill.skill_type_id', $category[0]->skill_type_id)->where('skill_scale_id', 2)->count() }}</span> -
                                            <span class="badge badge-success">{{ $enrollment->skill_evaluations->where('skill.skill_type_id', $category[0]->skill_type_id)->where('skill_scale_id', 3)->count() }}</span>
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
