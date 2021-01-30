@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        {{ $course->name }}
    </h2>
</section>
@endsection

@section('content')

<div class="row" id="app">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                @lang('Edit Grades')
            </div>

            <div class="card-body" style="overflow-x: scroll">
                <table class="table">
                    <tr>
                        <td></td>
                        @php $total = 0 @endphp
                        @foreach ($course_grade_types->sortBy('id') as $grade_type)
                            <td>
                                ({{$grade_type->category->name}})<br> <strong>{{$grade_type->name}}</strong>
                            </td>
                            @php $total += $grade_type->total @endphp
                        @endforeach
                        <td><strong>@lang('TOTAL')</strong></td>
                        <td><strong>@lang('Result')</strong></td>
                        <td><strong>@lang('Comment')</strong></td>
                    </tr>

                    @foreach ($enrollments as $enrollment)
                        <tr
                            is="enrollment-grades-component"
                            :key="{{ $enrollment->id }}"
                            :enrollment="{{ json_encode($enrollment) }}"
                            :course_grade_types="{{ json_encode($course_grade_types->sortBy('id')) }}"
                            :grades="{{ json_encode($grades->where('enrollment_id', $enrollment->id)) }}"
                        ></tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>

@endsection

@section('after_scripts')
    <script src="{{ mix('/js/app.js') }}"></script>
    <script src="{{ mix('/js/manifest.js') }}"></script>
    <script src="{{ mix('/js/vendor.js') }}"></script>
@endsection
