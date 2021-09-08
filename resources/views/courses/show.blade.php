@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        {{ $course->name ?? '' }}<br>
        <span class="text-muted text-uppercase font-weight-bold small">{{ $course->rhythm->name ?? '-' . ' ' . $course->level->name ?? '-' }}</span>
         |
        @if ($course->start_date && $course->end_date) <span class="text-muted small">{{ $course->formatted_start_date }} - {{ $course->formatted_end_date }}</span>@endif
    </h2>
</section>
@endsection


@section('content')

@include('partials.course_info')

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="card-header-actions">
                    @if ($course->accepts_new_students)
                        <button class="btn btn-primary" data-toggle="modal" data-target="#myModal">@lang('Enroll new student')</button>
                    @endif

                    <a href="/course/{{ $course->id }}/show/roster" class="btn btn-primary mr-3">{{__('Photo Roster')}}</a>
                </div>

            </div>

            <div class="card-body">

                <table id="studentsTable" class="table table-striped responsive" style="width:100%">
                    <thead>
                        <tr>
                            @if (in_array('lastname', config('app.course_view_columns')))
                                <th>@lang('Last Name')</th>
                            @endif

                            @if (in_array('firstname', config('app.course_view_columns')))
                                <th>@lang('First Name')</th>
                            @endif

                            @if (in_array('age', config('app.course_view_columns')))
                                <th>@lang('age')</th>
                            @endif

                            @if (in_array('status', config('app.course_view_columns')))
                                <th>@lang('Status')</th>
                            @endif

                            @if (in_array('birthdate', config('app.course_view_columns')))
                                <th>@lang('birthdate')</th>
                            @endif

                            @if (in_array('email', config('app.course_view_columns')))
                                <th>@lang('email')</th>
                            @endif

                            @if (in_array('phone', config('app.course_view_columns')))
                                <th>@lang('Phone Number')</th>
                            @endif

                            @if (in_array('book', config('app.course_view_columns')))
                                <th>@lang('Book')</th>
                            @endif

                            <th>@lang('actions')</th>
                        </tr>
                    </thead>

                    <tbody>
                        @foreach ($enrollments as $enrollment)
                        <tr>
                            @if (in_array('lastname', config('app.course_view_columns')))
                                <td>{{ $enrollment->student->lastname }}</td>
                            @endif

                            @if (in_array('firstname', config('app.course_view_columns')))
                                <td>{{ $enrollment->student->firstname }}</td>
                            @endif

                            @if (in_array('age', config('app.course_view_columns')))
                                <td>{{ $enrollment->student->student_age }}</td>
                            @endif

                            @if (in_array('age', config('app.course_view_columns')))
                                <td>{{ $enrollment->status }}</td>
                            @endif

                            @if (in_array('birthdate', config('app.course_view_columns')))
                                <td>{{ $enrollment->student->student_birthdate }}</td>
                            @endif

                            @if (in_array('email', config('app.course_view_columns')))
                                <td>{{ $enrollment->student->email }}</td>
                            @endif

                            @if (in_array('phone', config('app.course_view_columns')))
                                <td>{{ $enrollment->student->phone->implode('phone_number', ' - ') }}</td>
                            @endif

                            @if (in_array('book', config('app.course_view_columns')))
                                <td>{{ $enrollment->has_book_for_course }}</td>
                            @endif

                            <td><!-- available actions -->
                                <a href="/student/{{ $enrollment->student_id }}/show" class='btn btn-sm btn-secondary'>
                                    <i class='la la-briefcase'></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>


@endsection


@section('after_scripts')

@include('partials.enrollment_modal', ['course_id' => $course->parent])

<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/fh-3.1.6/datatables.min.css"/>

<!-- DATA TABLES -->
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/pdfmake.min.js"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.36/vfs_fonts.js"></script>
<script type="text/javascript" src="https://cdn.datatables.net/v/bs4/jszip-2.5.0/dt-1.10.20/b-1.6.1/b-html5-1.6.1/b-print-1.6.1/fh-3.1.6/datatables.min.js"></script>

<script>
    $(document).ready(() =>
        $('#studentsTable').DataTable(
            {
                "scrollX": true,
                paging: false,
                dom: 'Bfrtip',
                buttons: ['copy', 'excel', 'pdf', 'print']
            }
        ));
</script>


<link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
<script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
@endsection
