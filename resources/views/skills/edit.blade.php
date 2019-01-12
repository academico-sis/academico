@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Edit Course Skills')
    </h1>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('grades_edit')
                </div>
                
                <div class="box-tools pull-right">
                    <a class="btn btn-primary" href=""><i class="fa fa-plus"></i>Add Skill</a>
                </div>  
            </div>
            
            <div class="box-body">           
               <table class="table">
                   <thead>
                       <td>Skill Type</td>
                       <td>Level</td>
                       <td>Skill</td>
                   </thead>
                   
                   @foreach ($all_skills as $skill)
                   <tr>
                       <td></td>
                   </tr>
                   @endforeach
               </table>
            </div>
        </div>
    </div>
</div>

@endsection


@section('before_scripts')
   
@endsection


@section('after_scripts')

@endsection
