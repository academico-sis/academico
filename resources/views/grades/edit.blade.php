@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang_u('academico.grades_edit')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang_u('academico.grades_edit')
                </div>
                
                <div class="box-tools pull-right">
                    <a class="btn btn-primary" href=""><i class="fa fa-plus"></i> Add Grade Type to course</a>
                </div>  
            </div>
            
            <div class="box-body">           
                <table class="table">
                    
                    @foreach ($enrollments as $enrollment)
                    <tr>
                        <td>{{ $enrollment->student_data->firstname }}</td>
                        <td>
                            <ul>
                                @foreach ($enrollment->grades as $grade)
                                <li>{{ $grade->grade_type->name }} : 
                                    <a
                                        href="#"
                                        id="{{ $grade->id }}"
                                        class="grade"
                                        data-type="text"
                                        data-pk="{{ $grade->id }}"
                                        data-url="/grades"
                                        data-title="Enter new grade"
                                    >{{ $grade->grade }}</a>
                                    / {{ $grade->grade_type->total }}
                                    <button class="btn btn-xs btn-danger" onclick="deleteGrade({{ $grade->id }})"><i class="fa fa-trash"></i></button>
                                </li>
                                @endforeach
                            </ul>
                        </td>
                        {{-- todo add total --}}
                    </tr>
                    @endforeach
                </table>
            </div>
        </div>
    </div>
</div>

@endsection


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
