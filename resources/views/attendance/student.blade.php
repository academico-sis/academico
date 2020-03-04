@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
    @lang('Student Attendance Report')
    </h2>
</section>
@endsection


@section('content')

<div class="row">
    
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h5>@lang('Student :') {{ $student->name }}</h5>
                <h5>@lang('Course :')
                    <!-- Course selection dropdown -->
                    <div class="btn-group">
                        <div class="dropdown show">
                        <a class="btn btn-secondary dropdown-toggle" id="dropdownMenuLink" href="#" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">{{ $selectedCourse->period->name }} -> {{ $selectedCourse->name }}</a>
                            <div class="dropdown-menu" aria-labelledby="dropdownMenuLink">
                                @foreach ($studentEnrollments as $studentEnrollment)
                                    <li><a class="dropdown-item" href="{{ url()->current() }}?course_id={{ $studentEnrollment->course->id }}">{{ $studentEnrollment->course->period->name }} -> {{ $studentEnrollment->course->name }}</a></li>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </h5>
            
            </div><!-- /.card-header -->
            
            <div class="card-body" id="app">
                <student-attendance-report :attendanceratio="{{ $attendanceratio }}" :attendances="{{ $attendances }}" storeattendanceroute="{{ route('storeAttendance') }}"></student-attendance-report>
            </div>
        </div>
    </div>

</div>

@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection
