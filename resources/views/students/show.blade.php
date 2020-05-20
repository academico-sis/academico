@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h1>
        @lang('Student details for') {{ $student->name }}
    </h1>
</section>
@endsection


@section('content')
<div id="app">

<div class="row">

     @if ($student->getFirstMediaUrl() != null)
    <div class="col-md-2">
        <div class="card">
            <img src="{{$student->getMedia()->last()->getUrl('thumb')}}" style="width: 100%" />
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

    @if(backpack_user()->can('leads.manage'))
        <div class="row">
        <div class="col-md-12">
            <lead-status-component
            :student="{{ json_encode($student) }}"
            :leadtypes="{{ json_encode($lead_types) }}"
            route="{{ route('postLeadStatus') }}"
            >
            </lead-status-component>
            </div>
        </div>
    @endif
</div>
</div><!-- end of first row -->


<div class="row">

    @if (count($student->enrollments) > 0)
        <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                            @lang('Enrollments')

                        <div class="card-header-actions">
                                @if(backpack_user()->can('enrollments.edit'))

                            <a href="/availablecourse?student={{ $student->id }}" class="btn btn-sm btn-primary">
                                <i class="la la-user-plus"></i>
                            </a>
                            @endif

                        </div>
                    </div>

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
                                            <td>{{ $enrollment->status }}</td>
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
                </div>
            </div>
    @endif

</div>
</div>


@endsection

@section('before_scripts')


    <!-- Add additional user data Modal-->
    <div class="modal fade" id="userDataModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
          <div class="modal-content">
            <div class="modal-header">
              <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
              <h4 class="modal-title">@lang('Add a new contact')</h4>
            </div>
            <div class="modal-body">

                <form action="{{ route('addContact') }}" method="post">
                @csrf
                <input type="hidden" name="student_id" value="{{ $student->id }}">

                <div class="form-group">
                    <label for="firstname">@lang('Firstname')</label>
                    <input required type="text" id="firstname" name="firstname">
                </div>

                <div class="form-group">
                    <label for="lastname">@lang('Name')</label>
                    <input required type="text" id="lastname" name="lastname">
                </div>

                <div class="form-group">
                    <label for="email">@lang('Email')</label>
                    <input required type="email" id="email" name="email">
            </div>

            <div class="form-group">
                <label for="address">@lang('Address')</label>
                <input required type="text" id="address" name="address">
            </div>

            <div class="form-group">
                <label for="phone_number">@lang('Phone Number')</label>
                <input required type="text" id="phone_number" name="phone_number">
            </div>

            <div class="form-group">
                <label for="idnumber">@lang('ID Number')</label>
                <input required type="text" id="idnumber" name="idnumber">
            </div>


            <div class="modal-footer">
              <button type="button" class="btn btn-default" data-dismiss="modal">@lang('Close')</button>
              <button type="submit" class="btn btn-success">@lang('Save new Contact')</button>
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
