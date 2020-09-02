@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        @lang('Course Details')
    </h2>
</section>
@endsection

@section('content')

@include('partials.course_info')

<div class="row">	
	<div class="card">
		<div class="card-header">
		    <div class="card-header-actions">
		        <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">@lang('Enroll new student')</button>
		    </div>
		    <div class="card-header-actions">
		    	 <a href="/course/{{ $course->id }}/show/" class="btn btn-primary mr-3">Student List</a>
		    </div>
		</div>
	</div>
</div>


<div class="row">
    <div class="col-md-12">
    	@include('courses.components.rosterComponent')
    </div>
</div>


@endsection


@section('after_scripts')

@include('partials.enrollment_modal', ['course_id' => $course->parent])

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
  
@endsection