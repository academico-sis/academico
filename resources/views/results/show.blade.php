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

    <div class="col-md-6">
        <div class="card">
                <div class="card-header">
                    <a href="{{ route('student.show', ['id' => $enrollment->student_id]) }}">
                        <strong>{{ $enrollment->student->firstname }} {{ $enrollment->student->lastname }}</strong>
                    </a>
                </div>
            <div class="card-body">
                <a href="{{ route('enrollment.index', ['course_id' => $enrollment->course_id]) }}">
                    <p>{{ $enrollment->course->name }} ({{ $enrollment->course->period->name }})</p>
                </a>

                @if (config('certificates-generation.supported') && $enrollment->result && backpack_user()->hasRole(['admin', 'secretary']))
                    <a href="{{ route('enrollment-export-result', ['enrollment' => $enrollment->id]) }}" class="btn btn-primary @if ($enrollment->status_id === 1) disabled @endif">@lang('Generate grade report')</a>
                    <a href="{{ route('enrollment-export-certificate', ['enrollment' => $enrollment->id]) }}" class="btn btn-primary @if ($enrollment->status_id === 1) disabled @endif">@lang('Generate diploma')</a>
                    @if ($enrollment->enrollmentStatus->id === 1) {{__('The enrollment is unpaid') }} @endif
                @endif
            </div>
        </div>
    </div>



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
<div class="row">

@if (count($grades) > 0)
<div class="col-md-6">
        <div class="card">
            <div class="card-header with-border">
                <div class="card-title">
                    @lang('Grades')

                    <div class="card-header-actions">
                        <a href="/course/{{ $enrollment->course_id}}/grades" class="btn btn-primary">Retour aux notes du cours</a>
                    </div>
            </div>

            <div class="card-body">
                <table class="table">
                    @php $all_total = 0 @endphp
                    @php $all_grade = 0 @endphp
                    @foreach ($grades as $category)
                    @php $cat_total = 0 @endphp
                    @php $cat_grade = 0 @endphp
                    <thead>
                        <tr>
                            <th>{{ $category[0]->grade_type_category }}</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($category as $grade)
                        @php $cat_grade += $grade->grade @endphp
                        @php $cat_total += $grade->grade_type->total @endphp
                        <tr>
                            <td>{{ $grade->grade_type->name }}</td>
                            <td>{{ $grade->grade }} / {{ $grade->grade_type->total }}</td>
                        </tr>
                        @endforeach
                        <tr>
                            <td><strong>{{ __('Total') }}</strong></td>
                            <td><strong>{{ $cat_grade }} / {{ $cat_total }}</strong></td>
                        </tr>
                    </tbody>
                    @php $all_grade += $cat_grade @endphp
                    @php $all_total += $cat_total @endphp
                    @endforeach
                    <thead>
                        <tr>
                            <th><strong>{{ __('Total') }}</strong></th>
                            <th><strong>{{ $all_grade }} / {{ $all_total }}</strong></th>
                        </tr>
                    </thead>
                    </table>
            </div>
        </div>
    </div>
</div>
@endif

@if (count($skills) > 0)
    <div class="col-md-6">
        <div class="card">
            <div class="card-header with-border">
                @lang('Skills')
                <div class="card-header-actions">
                    @if($enrollment->course->evaluationType && $enrollment->course->evaluationType->skills->count() > 0 && $enrollment->course->course_enrollments_count > 0)
                        <a href="{{ route('studentSkillsEvaluation', ['enrollment' => $enrollment->id]) }}" class="btn btn-sm btn-link">
                            <i class="la la-th"></i> {{ __('Evaluate skills') }}
                        </a>
                        @endif
                </div>
            </div>

            <div class="card-body">
                    <table class="table">
                        @foreach ($skills as $skill)
                        <tr>
                            <td>{{ $skill->skill->name }}</td>
                            <td class="bg-{{ $skill->skill_scale->classes }}">{{ $skill->skill_scale->shortname }}</td>
                        </tr>
                        @endforeach
                    </table>
            </div>
        </div>
    </div>
    @endif


</div>

@endsection
