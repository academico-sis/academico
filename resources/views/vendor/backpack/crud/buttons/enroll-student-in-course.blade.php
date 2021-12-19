@php $course = App\Models\Course::find($crud->getRequest()->course_id) @endphp
@if ($course->accepts_new_students)
    <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">@lang('Enroll new student')</button>
@endif

@push('after_scripts')

@include('partials.enrollment_modal', ['course_id' => $course->parent])
<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
@endpush
