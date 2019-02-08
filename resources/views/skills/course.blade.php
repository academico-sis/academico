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
                    @lang('View Course Skills')
                </div>
                
                <div class="box-tools pull-right">
                    <a class="btn btn-primary" href="{{ backpack_url('skill') }}?course={{$course->id}}">
                        @lang('Edit skills for course')
                    </a>
                </div>  
            </div>
            
            <div class="box-body">
                
                @if($skills->count() > 0)
                    <table class="table table-striped" id="skillsTable">
                        <thead>
                            <td>@lang('Skill Type')</td>
                            <td>@lang('Level')</td>
                            <td>@lang('Skill')</td>
                        </thead>
                        <tbody>
                            @foreach ($skills as $skill)
                            <tr>
                                <td>{{ $skill->skill_type->shortname }}</td>
                                <td>{{ $skill->level->name }}</td>
                                <td>{{ $skill->name }}</td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                
                @else

                <p>@lang('There are no skills attached to this course')</p>

                @endif
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
