@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        @lang('Edit Course Skills') - {{ $course->name }}
    </h2>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-header-actions">
                    <a class="btn btn-primary" href="{{ route('course-skills-export', ['course' => $course->id]) }}">
                        @lang('Export skills')
                    </a>

                    <a class="btn btn-primary" href="#" data-toggle="modal" data-target="#skillsUploadModal">
                        @lang('Import skills')
                    </a>

                    <a class="btn btn-primary" href="{{ route('exportCourseSyllabus', ['course' => $course->id]) }}">
                        @lang('Export Course syllabus')
                    </a>
                </div>
            </div>
            
            <div class="card-body" id="app">
                
                    <skills-list :course=@json($course->id)></skills-list>
                
            </div>
        </div>
    </form>
</div>
</div>

@endsection

@section('after_scripts')
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

                <form action="/course/{{$course->id}}/skills/import" method="post" enctype="multipart/form-data">
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

    <script src="/js/app.js"></script>

    
@endsection
