<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->

@if(backpack_user()->hasRole(['admin', 'secretary']))
  <li class="header">@lang('COURSES')</li>

  <li><a href="{{ url('/') }}"><i class="fa fa-book"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>
  <li><a href="{{ backpack_url('course') }}"><i class="fa fa-book"></i> <span>@lang('Courses')</span></a></li>
  <li><a href="{{ backpack_url('student') }}"><i class="fa fa-user"></i> <span>@lang('Students')</span></a></li>
  <li><a href="{{ backpack_url('/enrollment?pending=true') }}"><i class="fa fa-credit-card"></i> <span>@lang('Pending')</span></a></li>

@endif

@if(backpack_user()->hasRole(['admin']))
  <li><a href="{{ backpack_url('preinvoice') }}"><i class="fa fa-dollar"></i> <span>@lang('Invoices')</span></a></li>
  <li><a href="{{ backpack_url('comment') }}"><i class="fa fa-pencil"></i> <span>@lang('Comments')</span></a></li>
  <li><a href="{{ backpack_url('attendance') }}"><i class="fa fa-calendar"></i> <span>@lang('Attendance')</span></a></li>

@endif


@if(backpack_user()->hasRole(['admin', 'manager']))
  <li class="header">@lang('HR')</li>
  <li><a href="{{ route('hrDashboard') }}"><i class="fa fa-calendar"></i> <span>@lang('HR')</span></a></li>

@endif

@if(backpack_user()->hasRole(['admin']))
  
  <li><a href="{{ backpack_url('leave') }}"><i class="fa fa-calendar"></i> <span>@lang('leave')</span></a></li>
  <li><a href="{{ backpack_url('remoteevent') }}"><i class="fa fa-calendar"></i> <span>@lang('Remote Events')</span></a></li>
  <li><a href="{{ backpack_url('event') }}"><i class="fa fa-calendar"></i> <span>@lang('Events')</span></a></li>

@endif


@if(backpack_user()->hasRole('admin'))
  <li class="header">@lang('SETTINGS')</li>

  <li class="treeview">
    <a href="#"><i class="fa fa-link"></i> <span>@lang('settings')</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
    <ul class="treeview-menu">
        <li><a href='{{ backpack_url('period') }}'><i class='fa fa-calendar'></i> <span>@lang('periods')</span></a></li>
        <li><a href='{{ backpack_url('level') }}'><i class='fa fa-star'></i> <span>@lang('levels')</span></a></li>
        <li><a href='{{ backpack_url('room') }}'><i class='fa fa-star'></i> <span>@lang('rooms')</span></a></li>
        <li><a href='{{ backpack_url('campus') }}'><i class='fa fa-star'></i> <span>@lang('campuses')</span></a></li>
        <li><a href='{{ backpack_url('rhythm') }}'><i class='fa fa-star'></i> <span>@lang('rhythms')</span></a></li>
        <li><a href='{{ backpack_url('year') }}'><i class='fa fa-star'></i> <span>@lang('Years')</span></a></li>
    </ul>
  </li>

  <!-- Users, Roles Permissions -->
  <li class="treeview">
      <a href="#"><i class="fa fa-group"></i> <span>@lang('users')</span> <i class="fa fa-angle-left pull-right"></i></a>
      <ul class="treeview-menu">
        <li><a href="{{ backpack_url('user') }}"><i class="fa fa-user"></i> <span>@lang('users')</span></a></li>
        <li><a href="{{ backpack_url('teacher') }}"><i class="fa fa-user"></i> <span>@lang('teachers')</span></a></li>
        <li><a href="{{ backpack_url('role') }}"><i class="fa fa-group"></i> <span>@lang('roles')</span></a></li>
        <li><a href="{{ backpack_url('permission') }}"><i class="fa fa-key"></i> <span>@lang('permissions')</span></a></li>
      </ul>
    </li>

@endif

@if(backpack_user()->hasRole('admin'))
  <li class="header">@lang('EVALUATION')</li>

  {{-- EVALUATION METHODS --}}
  <li class="treeview">
    <a href="#"><i class="fa fa-link"></i> <span>@lang('evaluation')</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
    <ul class="treeview-menu">
        <li><a href='{{ backpack_url('evaluationtype') }}'><span>@lang('Evaluation Types')</span></a></li>
        <li><a href='{{ backpack_url('gradetype') }}'><span>@lang('Grade Types')</span></a></li>
        <li><a href='{{ backpack_url('skill') }}'><span>@lang('Skills')</span></a></li>
        <li><a href='{{ backpack_url('skilltype') }}'><span>@lang('Skill Types')</span></a></li>
        <li><a href='{{ backpack_url('skillscale') }}'><span>@lang('Skill Scales')</span></a></li>
        <li><a href='{{ backpack_url('resulttype') }}'><span>@lang('Result Types')</span></a></li>

    </ul>
  </li>

@endif


@if(backpack_user()->hasRole(['admin', 'secretary']))
  <li class="header">@lang('CALENDARS')</li>
  <li class="treeview">
      <a href="#"><i class="fa fa-group"></i> <span>@lang('teachers')</span> <i class="fa fa-angle-left pull-right"></i></a>
      <ul class="treeview-menu">
        <li><a href="{{ route('teachersCalendar') }}"><i class="fa fa-binoculars"></i><span>@lang('Overview')</span></a></li>
        @foreach ($teachers as $teacher)
          <li><a href="{{ route('teacherCalendar', ['teacher' => $teacher->id]) }}"><span>{{ $teacher->name }}</span></a></li>
        @endforeach
    </ul>
  </li>


  <li class="treeview">
      <a href="#"><i class="fa fa-building"></i> <span>@lang('rooms')</span> <i class="fa fa-angle-left pull-right"></i></a>
      <ul class="treeview-menu">
        <li><a href="{{ route('roomsCalendar') }}"><i class="fa fa-binoculars"></i><span>@lang('Overview')</span></a></li>
        @foreach ($rooms as $room)
          <li><a href="{{ route('roomCalendar', ['room' => $room->id]) }}"><span>{{ $room->name }}</span></a></li>
        @endforeach
    </ul>
  </li>

@endif

@if(backpack_user()->hasRole(['admin', 'manager']))

  <li class="header">@lang('REPORTS')</li>
  <li><a href="{{ route('homeReport') }}"><i class="fa fa-book"></i> <span>@lang('Reports')</span></a></li>
  <li><a href="{{ route('rhythmReport') }}"><i class="fa fa-pie-chart"></i><span>@lang('Per rhythm')</span></a></li>
  <li><a href="{{ route('courseReport') }}"><i class="fa fa-graduation-cap"></i> <span>@lang('Per course')</span></a></li>
  {{-- <li><a href="{{ route('externalReport') }}"><i class="fa fa-bus"></i> <span>@lang('External Courses')</span></a></li> --}}

@endif

@if(backpack_user()->isTeacher()) {{-- todo transform this --}}
  <li class="header">@lang('TEACHER')</li>

  <li><a href="{{ route('teacherDashboard') }}"><i class="fa fa-home"></i> <span>@lang('Teacher Dashboard')</span></a></li>
  <li><a href="{{ route('teacherCalendar', ['teacher' => backpack_user()->id]) }}"><i class="fa fa-calendar"></i> <span>@lang('My Schedule')</span></a></li>
  <li><a href="{{ route('teacherHours', ['teacher' => backpack_user()->id]) }}"><i class="fa fa-clock-o"></i> <span>@lang('My Hours')</span></a></li>

@endif

@if(backpack_user()->isTeacher() || backpack_user()->isStudent()) {{-- todo transform this --}}
  <li><a href="{{ route('moodleLogin') }}"><i class="fa fa-clock-o"></i> <span>@lang('SPHERE')</span></a></li>

@endif