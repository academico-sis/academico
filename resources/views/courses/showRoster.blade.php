@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        @lang('Course Details')
    </h2>
</section>
@endsection

@section('content')

@include('partials.course_info', ['widget' => ['course' => $course]])

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                @if (backpack_user()->hasRole('admin'))
                    <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">@lang('Enroll new student')</button>
                     <a href="/enrollment?course_id={{ $course->id }}" class="btn btn-primary mr-3">@lang('Students List')</a>
                @else
                    <a href="/course/{{ $course->id }}/show" class="btn btn-primary mr-3">@lang('Students List')</a>
                @endif
            </div>
        </div>
    </div>
</div>


<div class="row">
	@foreach ($enrollments as $enrollment)
	<div class="col-sm-6 col-md-3 col-lg-2">
        <div class="card">
            @if ($enrollment->student->getMedia('profile-picture')->count() > 0)
                <img class="card-img-top" src="{{ $enrollment->student->getMedia('profile-picture')->last()->getUrl('thumb') }}"/>
            @else
                <img class="card-img-top" src="{{ asset('user-icon-placeholder.png') }}"/>
            @endif
            <div class="card-body">
            <a href="{{ route('student.show', ['id' => $enrollment->student_id]) }}">
                <h5>{{$enrollment->student->name}}</h5>
            </a>
            </div>
        </div>
    </div>
@endforeach
</div>

@endsection


@section('after_scripts')

@include('partials.enrollment_modal', ['course_id' => $course->parent])

<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>

@endsection
