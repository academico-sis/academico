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

                    <a class="btn btn-primary" data-toggle="modal" data-target="#skillsUploadModal">
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



    <!-- Skillset upload Modal-->
    <div class="modal fade" id="skillsUploadModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">@lang('Upload skillset file')</h4>
            </div>
            <div class="modal-body">
                <h4 class="danger">@lang('This will erase all skills currently associated to the course')</h4>

                <form action="{{ route('course-skills-import', ['course_id' => $course->id]) }}" method="post" enctype="multipart/form-data">
                @csrf
                
                <div class="form-group">
                    <label for="skillset">@lang('Skillset File')</label>
                    <input type="file" id="skillset" name="skillset">
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
@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>

    
@endsection
