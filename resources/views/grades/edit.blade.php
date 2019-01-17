@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Edit Grades')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('Edit Grades')
                </div>
                
                <div class="box-tools pull-right">
                    <a class="btn btn-primary" data-toggle="modal" data-target="#gradeTypeModal">
                        <i class="fa fa-plus"></i> @lang('Add Grade Type to Course')
                    </a>
                </div>
            </div>
            
            <div class="box-body">           
                <table class="table">
                    <tr>
                        <td></td>
                        @php $total = 0; @endphp
                        @foreach ($course_grade_types->sortBy('id') as $grade_type)
                            <td>
                                {{$grade_type->name}}
                                <button onclick="return confirm('Are you sure? This will delete all grades for this grade type.')?removeGradeType({{$grade_type->id}}):'';">(x)</button>
                            </td>
                            @php $total += $grade_type->total; @endphp
                        @endforeach
                        <td><strong>@lang('TOTAL')</strong></td>
                    </tr>

                    @foreach ($enrollments as $enrollment)
                    @php $student_total = 0; @endphp
                    <tr>
                        <td>{{ $enrollment->student_data->name }}</td>
                        
                        @foreach ($course_grade_types->sortBy('id') as $grade_type)
                        <td>
                            @foreach($grades->where('user_id', $enrollment->student_data->id)->where('grade_type_id', $grade_type->id) as $grade)
                            @php $student_total += $grade->grade; @endphp
                                <p>
                                    <a
                                        href="#"
                                        id="{{ $grade->id }}"
                                        class="grade"
                                        data-type="text"
                                        data-pk="{{ $grade->id }}"
                                        data-url="/grades"
                                        data-title="Enter new grade"
                                    >{{ $grade->grade }}</a> / {{ $grade_type->total }}
                                </p>
                            @endforeach
                            </td>
                        @endforeach

                        <td>
                            <strong>{{ $student_total }} / {{ $total }}</strong>
                        </td>

                    </tr>
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
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title">@lang('Add a new grade type to course')</h4>
            </div>
            <div class="modal-body">
                <form action="/course/gradetype" method="post">
                    @csrf
                    <input type="hidden" name="course_id" value="{{ $course->id }}">
                    
                    <div class="form-group">
                        <select name="grade_type_id" required>
                            @foreach ($all_grade_types as $grade_type)
                                <option value="{{ $grade_type->id }}">{{ $grade_type->name }}</option>
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
    <script>
        
    function deleteGrade(id)
        {
            
            axios.delete('/grades', {
                
                params: { id }
                
                } )

            .then(function (response) {
                document.location.reload(true);
            })
            .catch(function (error) {
                console.log(error);
            });

        }

        
        function removeGradeType(gradetype)
        {
            axios.delete('/course/{{$course->id}}/gradetype/'+gradetype, {
                
                } )

            .then(function (response) {
                document.location.reload(true);
            })
            .catch(function (error) {
                console.log(error);
            });

            
        }

    </script>
@endsection

@section('after_scripts')

<link href="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/css/bootstrap-editable.css" rel="stylesheet"/>
<script src="//cdnjs.cloudflare.com/ajax/libs/x-editable/1.5.0/bootstrap3-editable/js/bootstrap-editable.min.js"></script>
<script src="https://unpkg.com/axios/dist/axios.min.js"></script>

<script>
$(document).ready(function() {
    $('.grade').editable();

    $.ajaxSetup({
  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

});
</script>
@endsection
