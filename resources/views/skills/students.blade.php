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
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('View Skills for Group')
                </div>

                <div class="box-tools pull-right">
                </div>  
            </div>
            
            <div class="box-body" id="app">    
               <table class="table">
                    @foreach ($students as $student)
                        <tr>
                            <td>
                                <a href="/course/{{$course->id}}/skillsevaluation/{{$student->student_data->id}}">{{ $student->student_data->firstname }}</a>
                            </td>
                            @foreach ($skills as $skill)
                                <td>{{ $skill->name }}</td>
                            @endforeach
                        </tr>
                    @endforeach
               </table>
              
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
