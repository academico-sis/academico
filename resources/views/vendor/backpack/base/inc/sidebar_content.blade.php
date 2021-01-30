<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->

<li class='nav-item'><a class='nav-link' href="{{ url('/') }}"><i class="nav-icon la la-book"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>

@if(backpack_user()->hasRole(['admin', 'secretary']))
  <li class="nav-title">@lang('ADMINISTRATION')</li>
  <li class='nav-item'><a class='nav-link' href="{{ route('course-view-find') }}"><i class="nav-icon la la-book"></i> <span>@lang('Courses')</span></a></li>
  <li class='nav-item'><a class='nav-link' href="{{ backpack_url('student') }}"><i class="nav-icon la la-user"></i> <span>@lang('Students')</span></a></li>
  <li class='nav-item'><a class='nav-link' href="{{ backpack_url('/enrollment') }}"><i class="nav-icon la la-credit-card"></i> <span>@lang('Enrollments')</span></a></li>
  <li class='nav-item'><a class='nav-link' href="{{ backpack_url('attendance') }}"><i class="nav-icon la la-calendar"></i> <span>@lang('Attendance')</span></a></li>
  <li class='nav-item'><a class='nav-link' href="{{ backpack_url('/result') }}"><i class="nav-icon la la-graduation-cap"></i> <span>@lang('Results')</span></a></li>

  <li class="nav-title">@lang('CALENDARS')</li>
  <li class='nav-item'><a class='nav-link' href="{{ route('teachersCalendar') }}"><i class="nav-icon la la-binoculars"></i><span>@lang('Teachers overview')</span></a></li>
  <li class='nav-item'><a class='nav-link' href="{{ route('roomsCalendar') }}"><i class="nav-icon la la-binoculars"></i><span>@lang('Rooms overview')</span></a></li>
  @if (isset($teachers))
    <li class="nav-item nav-dropdown"><a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i> @lang('teachers')</a>
        <ul class="nav-dropdown-items">
          @foreach ($teachers as $teacher)
            <li class='nav-item'><a class='nav-link' href="{{ route('teacherCalendar', ['teacher' => $teacher->id]) }}"><span>{{ $teacher->name }}</span></a></li>
          @endforeach
      </ul>
    </li>
  @endif

  @if(isset($rooms))
    <li class="nav-item nav-dropdown"><a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-building"></i> @lang('rooms')</a>
        <ul class="nav-dropdown-items">
          @foreach ($rooms as $room)
            <li class='nav-item'><a class='nav-link' href="{{ route('roomCalendar', ['room' => $room->id]) }}"><span>{{ $room->name }}</span></a></li>
          @endforeach
      </ul>
    </li>
  @endif

@endif


@if(backpack_user()->hasRole('admin'))
  <li class="nav-title">@lang('HR')</li>
  <li class='nav-item'><a class='nav-link' href="{{ route('hrDashboard') }}"><i class="nav-icon la la-calendar"></i> <span>@lang('HR')</span></a></li>

<li class="nav-item nav-dropdown">
    <a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-calendar"></i> @lang('Leave')</a>
    <ul class="nav-dropdown-items">
      <li class='nav-item'><a class='nav-link' href="{{ backpack_url('leave/teachers') }}"><i class="nav-icon la la-calendar"></i> <span>@lang('Overview')</span></a></li>
      <li class='nav-item'><a class='nav-link' href="{{ backpack_url('leave') }}"><i class="nav-icon la la-calendar"></i> <span>@lang('Manage leaves')</span></a></li>
    </ul>
</li>

  <li class='nav-item'><a class='nav-link' href="{{ backpack_url('remoteevent') }}"><i class="nav-icon la la-calendar"></i> <span>@lang('Remote Events')</span></a></li>
  <li class='nav-item'><a class='nav-link' href="{{ backpack_url('event') }}"><i class="nav-icon la la-calendar"></i> <span>@lang('Events')</span></a></li>

@endif



@if(backpack_user()->hasRole(['admin', 'manager']))

  <li class="nav-title">@lang('REPORTS')</li>

  <li class='nav-item'><a class='nav-link' href="{{ route('homeReport') }}"><i class="nav-icon la la-users"></i> <span>@lang('Students')</span></a></li>
  <li class='nav-item'><a class='nav-link' href="{{ route('rhythmReport') }}"><i class="nav-icon la la-pie-chart"></i><span>@lang('Per rhythm')</span></a></li>
  <li class='nav-item'><a class='nav-link' href="{{ route('levelReport') }}"><i class="nav-icon la la-pie-chart"></i><span>@lang('Per level')</span></a></li>
  <li class='nav-item'><a class='nav-link' href="{{ route('courseReport') }}"><i class="nav-icon la la-graduation-cap"></i><span>@lang('Per course')</span></a></li>

@endif

@if(backpack_user()->hasRole(['admin', 'secretary']))
<li class="nav-title">@lang('SETTINGS')</li>

<li class="nav-item nav-dropdown"><a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-link"></i> @lang('Invoicing')</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('discount') }}'><i class='la la-percent'></i> <span>@lang('Discounts')</span></a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('fee') }}'><i class='la la-dollar'></i> <span>@lang('Fees')</span></a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('paymentmethod') }}'><i class='la la-dollar'></i> <span>@lang('Payment methods')</span></a></li>
    </ul>
</li>

@if(backpack_user()->hasRole('admin'))

<li class="nav-item nav-dropdown"><a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-link"></i> @lang('Settings')</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('institution') }}'><i class='nav-icon la la-building'></i> @lang('Institutions')</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('period') }}'><i class='la la-calendar'></i> <span>@lang('periods')</span></a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('level') }}'><i class='la la-star'></i> <span>@lang('levels')</span></a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('room') }}'><i class='la la-star'></i> <span>@lang('rooms')</span></a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('campus') }}'><i class='la la-star'></i> <span>@lang('campuses')</span></a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('rhythm') }}'><i class='la la-star'></i> <span>@lang('rhythms')</span></a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('year') }}'><i class='la la-star'></i> <span>@lang('Years')</span></a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('book') }}'><i class='la la-book'></i> <span>@lang('Books')</span></a></li>
    </ul>
</li>

<!-- Users, Roles Permissions -->
<li class="nav-item nav-dropdown"><a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i> @lang('Users')</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href="{{ backpack_url('user') }}"><i class="nav-icon la la-user"></i> <span>@lang('Users')</span></a></li>
        <li class='nav-item'><a class='nav-link' href="{{ backpack_url('teacher') }}"><i class="nav-icon la la-user"></i> <span>@lang('Teachers')</span></a></li>
    </ul>
</li>

<li class="nav-item nav-dropdown"><a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-link"></i> @lang('Evaluation')</a>
    <ul class="nav-dropdown-items">
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('evaluationtype') }}'><span>@lang('Evaluation Types')</span></a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('gradetype') }}'><span>@lang('Grade Types')</span></a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('gradetypecategory') }}'><i class='nav-icon la la-question'></i> @lang('Grade Type Categories')</a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('skill') }}'><span>@lang('Skills')</span></a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('skilltype') }}'><span>@lang('Skill Types')</span></a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('skillscale') }}'><span>@lang('Skill Scales')</span></a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('resulttype') }}'><span>@lang('Result Types')</span></a></li>
    </ul>
</li>

@endif
@endif

@if(backpack_user()->isTeacher())
  <li class="nav-title">@lang('TEACHER')</li>

  <li class='nav-item'><a class='nav-link' href="{{ route('teacherDashboard') }}"><i class="nav-icon la la-home"></i> <span>@lang('Teacher Dashboard')</span></a></li>
  <li class='nav-item'><a class='nav-link' href="{{ route('teacherCalendar', ['teacher' => backpack_user()->teacher_id]) }}"><i class="nav-icon la la-calendar"></i> <span>@lang('My Schedule')</span></a></li>
  <li class='nav-item'><a class='nav-link' href="{{ route('teacherHours', ['teacher' => backpack_user()->teacher_id]) }}"><i class="nav-icon la la-clock-o"></i> <span>@lang('My Hours')</span></a></li>
@endif

@if(backpack_user()->isStudent())
  <li class="nav-item"><a class='nav-link' href="{{ route('backpack.account.edit_info') }}"><i class="la la-user"></i> <span>{{ trans('backpack::base.my_account') }}</span></a></li>
@endif
