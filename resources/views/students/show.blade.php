@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h1>
        @lang('Student details for') {{ $student->name }}
        <a href="{{ route('email-dashboard', ['student'=> $student->email]) }}" class="btn btn-sm btn-primary">
            <i class="la la-send"></i> Send Email
        </a>
    </h1>
</section>
@endsection


@section('content')
<div>

<div class="row">

     @if ($student->getMedia('profile-picture')->count() > 0)
    <div class="col-md-2">
        <div class="card">
            <img src="{{$student->getMedia('profile-picture')->last()->getUrl('thumb')}}" style="width: 100%" />
        </div>
    </div>
    @endif

    <div class="col-md-6">
        <student-contacts-component :student="{{ $student }}" :contacts="{{ $student->contacts }}" writeaccess="{{ $writeaccess }}"></student-contacts-component>
    </div>


    <div class="col-md-4">

    @if(backpack_user()->can('comments.edit'))
        <div class="row">
        <div class="col-md-12">
            <student-comments
            :comments="{{ json_encode($comments) }}"
            :id="{{ json_encode($student->id) }}"
            :type="'App\\Models\\Student'"
            route="{{ route('storeComment') }}">
            </student-comments>
            </div>
        </div>
    @endif
</div>
</div><!-- end of first row -->


<div class="row">

    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                @lang('Enrollments')

                <div class="card-header-actions">
                    @if(backpack_user()->can('enrollments.edit'))

                    <a href="{{ route('get-courses-list', ['mode' => 'enroll', 'student_id' => $student->id]) }}" class="btn btn-sm btn-primary">
                        <i class="la la-user-plus"></i>
                    </a>
                    @endif

                </div>
            </div>

            @if (count($student->enrollments) > 0)
                    <div class="card-body">
                        <table class="table table-striped">
                            <thead>
                                <th>@lang('Date')</th>
                                <th>@lang('Enrollment ID')</th>
                                @if(backpack_user()->can('enrollments.edit'))
                                    <th>@lang('Status')</th>
                                @endif
                                <th>@lang('Course')</th>
                                <th>@lang('Teacher')</th>
                                <th>@lang('Period')</th>
                                <th>@lang('Result')</th>
                                <th>@lang('Attendance')</th>
                            </thead>

                            <tbody>
                                @foreach($student->enrollments as $enrollment)
                                    <tr>
                                        <td>{{ $enrollment->date }}</td>
                                        <td>
                                            <a href="/enrollment/{{ $enrollment->id }}/show">{{ $enrollment->id }}</a>
                                        </td>
                                        @if(backpack_user()->can('enrollments.edit'))
                                            <td>
                                                {{ $enrollment->status }}
                                            </td>
                                        @endif
                                        <td>{{ $enrollment->course->name }}</td>
                                        <td>{{ $enrollment->course->teacher->name ?? '-'}}</td>
                                        <td>{{ $enrollment->course->period->name }}</td>
                                        <td>
                                            @if(isset($enrollment->result))
                                            {{ $enrollment->result['result_name']['name'] }}
                                            <a href="/result/{{ $enrollment->id }}/show" class="btn btn-sm btn-info">
                                                <i class="la la-eye"></i>
                                            </a>
                                            @endif
                                        </td>
                                        <td>
                                            @if(isset($enrollment->attendance_ratio))
                                                {{ $enrollment->attendance_ratio }}%
                                                <a href="{{ route('studentAttendance', ['student' => $enrollment->student_id]) }}?course_id={{ $enrollment->course_id }}" class="btn btn-sm btn-info">
                                                    <i class="la la-eye"></i>
                                                </a>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @endif
                </div>
            </div>

</div>{{-- end of enrollments row --}}

@if(backpack_user()->can('enrollments.edit') && config('academico.books_module'))
    <student-books-component :student="{{ json_encode($student) }}" :books="{{ json_encode($student->books) }}"></student-books-component>
@endif

</div>


@endsection

@section('before_scripts')

@include('partials.create_new_contact')

@if (config('academico.books_module'))
    @include('partials.add_book_to_student')
@endif
@endsection
