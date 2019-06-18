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

                    <a class="btn btn-primary" href="{{ route('course-skills-export', ['course_id' => $course->id]) }}">
                        @lang('Export skills')
                    </a>

                    <a class="btn btn-primary" href="{{ backpack_url('skill') }}?course={{$course->id}}">
                        @lang('Import skills')
                    </a>
                </div>  
            </div>
            
            <div class="box-body" id="app">
                
                    <skills-list :course={!! json_encode($course->id) !!}></skills-list>
                
            </div>
        </div>
    </form>
</div>
</div>

@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>

    
@endsection
