@extends('backpack::layout')

@section('header')
<section class="content-header">
    <h1>
        @lang('Student details for') {{ $student->name }}
    </h1>
</section>
@endsection


@section('content')
<div id="app">

<div class="row">

    <div class="col-md-4">
        <div class="box">
            <div class="box-header with-border">
                <div class="box-title">
                    @lang('Student Info')
                </div>
                <div class="box-tools pull-right">

                    @if(backpack_user()->can('enrollments.edit'))
                        <a class="btn btn-xs btn-warning" href="/student/{{$student->id}}/edit">
                            <i class="fa fa-edit"></i>
                        </a>
                    @endif

                    @if(backpack_user()->can('enrollments.edit'))
                        <a class="btn btn-xs btn-primary" data-toggle="modal" data-target="#userDataModal">
                            <i class="fa fa-plus"></i>
                        </a>
                    @endif

                </div>
            </div>
            <div class="box-body">
                @include('students.student_info')
            </div>
        </div>
    </div>

    @foreach ($student->contacts as $contact)
    <div class="col-md-4">
            <div class="box">
                <div class="box-header with-border">
                    <div class="box-title">
                        @lang('Additional Contact')
                        @if(isset($contact->relationship))
                        ({{ $contact->relationship->name }})
                        @endif
                    </div>

                    <div class="box-tools pull-right">
                        @if(backpack_user()->can('enrollments.edit'))
                            <a class="btn btn-xs btn-warning" href="/contact/{{$contact->id}}/edit">
                                <i class="fa fa-edit"></i>
                            </a>
                        @endif

                    </div>
                </div>
                
                <div class="box-body">
                    @include('students.additional_info')
                </div>
            </div>
        </div>
    @endforeach

{{-- </div>


<div class="row">
 --}}

 @if(backpack_user()->can('enrollments.edit'))
    <div class="col-md-4">
        <student-comments
        :comments="{{ json_encode($comments) }}"
        :student="{{ json_encode($student) }}"
        route="{{ url('comment') }}">
        </student-comments>
    </div>
@endif

    @if (count($student->enrollments) > 0)
        <div class="col-md-8">
                <div class="box">
                    <div class="box-header with-border">
                        <div class="box-title">
                            @lang('Enrollments')
                        </div>
                        <div class="box-tools pull-right">
                                @if(backpack_user()->can('enrollments.edit'))

                            <a href="/availablecourse?student={{ $student->id }}" class="btn btn-xs btn-primary">
                                <i class="fa fa-user-plus"></i>
                            </a>
                            @endif

                        </div>
                    </div>
                    
                    <div class="box-body">
                        <table class="table table-striped">
                            <thead>
                                <th>@lang('Date')</th>
                                <th>@lang('Enrollment ID')</th>
                                <th>@lang('Course')</th>
                                <th>@lang('Period')</th>
                                @if(backpack_user()->can('enrollments.edit'))
                                    <th>@lang('Status')</th>
                                @endif

                                <th>@lang('Result')</th>
                            </thead>

                            <tbody>
                                @foreach($student->enrollments as $enrollment)
                                    <tr>
                                        <td>{{ $enrollment->date }}</td>
                                        <td>
                                            {{-- <a href="/enrollment/{{ $enrollment->id }}"> todo reenable --}}
                                            {{ $enrollment->id }}
                                           {{--  </a> --}}
                                        </td>
                                        <td>{{ $enrollment->course->name }}</td>
                                        <td>{{ $enrollment->course->period->name }}</td>
                                        @if(backpack_user()->can('enrollments.edit'))
                                            <td>{{ $enrollment->status }}</td>
                                        @endif
                                        <td>
                                            @if(isset($enrollment->result))
                                            {{ $enrollment->result['result_name']['name'] }}
                                            <a href="/result/{{ $enrollment->result['id'] }}" class="btn btn-xs btn-info">
                                                <i class="fa fa-eye"></i>
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
                    <input type="text" id="firstname" name="firstname">
                </div>

                <div class="form-group">
                    <label for="lastname">@lang('Name')</label>
                    <input type="text" id="lastname" name="lastname">
                </div>

                <div class="form-group">
                    <label for="email">@lang('Email')</label>
                    <input type="text" id="email" name="email">
            </div>

            <div class="form-group">
                <label for="address">@lang('Address')</label>
                <input type="text" id="address" name="address">
            </div>

            <div class="form-group">
                <label for="phone_number">@lang('Phone Number')</label>
                <input type="text" id="phone_number" name="phone_number">
            </div>

            <div class="form-group">
                <label for="idnumber">@lang('ID Number')</label>
                <input type="text" id="idnumber" name="idnumber">
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
