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
            <form action="/course/{{$course->id}}/skills" method="post">
                @method('patch')
                @csrf
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('Edit Course Skills')
                </div>

                <div class="box-tools pull-right">
                    <button class="btn btn-success pull-right" type="submit">
                        @lang('Save skills for course')
                    </button>
                </div>  
            </div>
            
            <div class="box-body">           
               <table class="table table-striped" id="skillsTable">
                   <thead>
                       <td></td>
                       <td>Skill Type</td>
                       <td>Level</td>
                       <td>Skill</td>
                   </thead>
                   <tbody>
                       @foreach ($skills as $skill)
                            <tr>
                                <td><input type="checkbox" name="skill[{{$skill->id}}]" id="skill[{{$skill->id}}]" @if($skill->attached == '1') checked @endif></td>
                                <td>{{ $skill->level->name }}</td>
                                <td>{{ $skill->skill_type->shortname }}</td>
                                <td>
                                    <label for="skill[{{$skill->id}}]">{{ $skill->name }}</label>
                                </td>
                                <td></td>
                            </tr>
                        @endforeach
                   </tbody>
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

@endsection
