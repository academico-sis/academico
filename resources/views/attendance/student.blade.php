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
            
            <div class="card-body">

                <div class="row">

                    <div class="col-sm-6 col-md-3">

                        <div class="card">
                            <div class="card-body p-3 d-flex align-items-center"><i class="la la-user bg-primary p-3 font-2xl mr-3"></i>
                                <div>
                                    <div class="text-value-sm text-primary">{{ $attendanceratio }}%</div>
                                    <div class="text-muted text-uppercase font-weight-bold small">@lang('Attendance Ratio')</div>
                                </div>
                            </div>
                        </div>
                    </div>


                    <div class="col-sm-6 col-md-3">
                        <div class="card">
                            <div class="card-body p-3 d-flex align-items-center"><i class="la la-user-times bg-danger p-3 font-2xl mr-3"></i>
                                <div>
                                    <div class="text-value-sm text-primary">{{ $attendances->where('attendance_type_id', 4)->count() + $attendances->where('attendance_type_id', 3)->count() }}</div>
                                    <div class="text-muted text-uppercase font-weight-bold small">@lang('Number of Absences')</div>
                                </div>
                            </div>
                        </div>
                    </div>


                </div>

                <div class="row">
                    <div class="col-md-12">
                    <div class="card">
                    <div class="card-body">
                    <table class="table" id="attendanceTable">
                        <thead>
                            <th>@lang('Event')</th>
                            <th>@lang('Date')</th>
                            <th>@lang('Attendance status')</th>
                        </thead>
                        <tbody>
                        @foreach ($attendances as $attendance)
                            <tr>
                            <td>{{ $attendance->event->name }}</td>
                            <td>{{ Carbon\Carbon::parse($attendance->event->start)->locale(app()->getLocale())->isoFormat('Do MMM YYYY') }}</td>
                            <td>
                                <label class="badge badge-{{ $attendance->attendance_type->class }}">
                                {!! $attendance->attendance_type->icon !!}
                                {{ $attendance->attendance_type->translated_name }}
                            </td>
                            </tr>
                        @endforeach
                    </tbody>
                    </table>
                    </div>
                    <div class="card-footer">
            <label class="badge badge-success">
                P <i class="la la-user"></i>
            </label> : @lang('Present')
            - 
            <label class="badge badge-warning">
                PP <i class="la la-clock-o"></i>
            </label> : @lang('Partial presence (arrived late or left early)')
            - 
            <label class="badge badge-info">
                AJ <i class="la la-exclamation"></i>
            </label> : @lang('justified absence')
            - 
            <label class="badge badge-danger">
                A <i class="la la-user-times"></i>
            </label> : @lang('unjustified absence')
            </div>
            </div>
                    
                    </div>
                </div>
                </div>
            </div>
        </div>

</div>

@endsection

@section('after_scripts')
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/datatables.min.css"/>
 
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
    <script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/datatables.min.js"></script>
<script>
    $(document).ready(function() {
    $('#attendanceTable').DataTable( {
    dom: 'Bfrtip',
    "paging": false,
    buttons: [
        'excel',
        'pdf',
        'print'
    ]
} );
} );
</script>
@endsection
