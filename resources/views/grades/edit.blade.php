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

                <div class="card-header-actions">
                    <a class="btn btn-primary" data-toggle="modal" data-target="#gradeTypeModal">
                        <i class="la la-plus"></i> @lang('Add Grade Type to Course')
                    </a>
                </div>
            </div>

            <div class="card-body" style="overflow-x: scroll">
                <table class="table">
                    <tr>
                        <td></td>
                        @php $total = 0 @endphp
                        @foreach ($course_grade_types->sortBy('id') as $grade_type)
                            <td>
                                ({{$grade_type->category->name}})<br> <strong>{{$grade_type->name}}</strong>
                                <br>
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure? This will delete all grades for this grade type.')?removeGradeType({{$grade_type->id}}):'';"><i class="la la-trash"></i></button>
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


<!-- New GradeType Modal-->
<div class="modal fade" id="gradeTypeModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title">@lang('Add a new grade type to course')</h4>
                <div class="modal-header-action">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            </div>
            <div class="modal-body">
                <form action="/course/gradetype" method="post">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">

                    <div class="form-group">
                        <select name="grade_type_id" required>
                            @foreach ($all_grade_types as $grade_type)
                                <option value="{{ $grade_type->id }}">({{$grade_type->category->name}}) {{ $grade_type->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn-success">@lang('Save')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>


@section('before_scripts')
    {{-- TODO migrate this to new Vue components --}}
    <script>

        function removeGradeType(gradetype)
        {
            axios.delete(`/course/{{$course->id}}/gradetype/${gradetype}`, {

                } )

            .then(response => document.location.reload(true))
            .catch(error => console.log(error));

        }

    </script>
@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection
