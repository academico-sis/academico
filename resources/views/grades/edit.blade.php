@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        @lang('Edit Grades')
    </h2>
</section>
@endsection

@section('after_styles')
<style>
            .glyphicon-ok::before {
                content: "\f00c";
            }
            .glyphicon-remove::before {
                content: "\f00d";
            }
            .glyphicon {
                font-family: 'Font Awesome 5 Free';
                font-weight: 900;
                font-style: normal;
            }
</style>
<script src="https://kit.fontawesome.com/cedee4c07c.js" crossorigin="anonymous"></script>
@endsection
@section('content')

<div class="row">
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
                        @php $total = 0; @endphp
                        @foreach ($course_grade_types->sortBy('id') as $grade_type)
                            <td>
                                ({{$grade_type->category->name}})<br> <strong>{{$grade_type->name}}</strong>
                                <br>
                                <button class="btn btn-sm btn-danger" onclick="return confirm('Are you sure? This will delete all grades for this grade type.')?removeGradeType({{$grade_type->id}}):'';"><i class="la la-trash"></i></button>
                            </td>
                            @php $total += $grade_type->total; @endphp
                        @endforeach
                        <td><strong>@lang('TOTAL')</strong></td>
                        <td><strong>@lang('Result')</strong></td>
                        <td><strong>@lang('Comment')</strong></td>
                    </tr>

                    @foreach ($enrollments as $enrollment)
                    @php $student_total = 0; @endphp
                    <tr>
                        <td>{{ $enrollment->student_name }}</td>
                        
                        @foreach ($course_grade_types->sortBy('id') as $grade_type)
                        <td>
                            @foreach($grades->where('student_id', $enrollment->student->id)->where('grade_type_id', $grade_type->id) as $grade)
                            @php $student_total += $grade->grade; @endphp
                                <p>
                                    <a
                                        href="#"
                                        id="{{ $grade->id }}"
                                        class="grade"
                                        data-type="number"
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

                        <td>
                            {{ $enrollment->result_name }}
                            <a href="/result/{{ $enrollment->id}}/show">@lang('Edit')</a>
                        </td>

                        <td>
                            @foreach($enrollment->result->comments as $comment)
                            <p>{{ $comment->body }}</p>
                            @endforeach
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
    $('.grade').editable({mode: 'inline'});
    
    //make username required
    $('.grade').editable('option', 'validate', function(v) {
    if(!v) return 'Required field!';
});

    $.ajaxSetup({
  headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
});

});
</script>
@endsection
