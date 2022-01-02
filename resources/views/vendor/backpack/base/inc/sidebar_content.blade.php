<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->

<li class='nav-item'><a class='nav-link' href="{{ url('/') }}"><i class="nav-icon la la-book"></i> <span>{{ __('Home') }}</span></a></li>

@if(backpack_user()->hasRole(['admin', 'secretary']))
    <li class="nav-title">@lang('COURSES')</li>
        <li class='nav-item'><a class='nav-link' href="{{ route('course-view-find') }}"><i class="nav-icon la la-book"></i> <span>@lang('Internal Courses')</span></a></li>
        @if (config('settings.external_courses_enabled'))
            <li class='nav-item'><a class='nav-link' href="{{ backpack_url('externalcourse') }}"><i class="nav-icon la la-bus"></i> <span>@lang('External Courses')</span></a></li>
        @endif
    <li class="nav-title">@lang('ADMINISTRATION')</li>
        <li class='nav-item'><a class='nav-link' href="{{ backpack_url('student') }}"><i class="nav-icon la la-user"></i> <span>@lang('Students')</span></a></li>
        <li class='nav-item'><a class='nav-link' href="{{ backpack_url('enrollment') }}"><i class="nav-icon la la-credit-card"></i> <span>@lang('Enrollments')</span></a></li>
        <li class='nav-item'><a class='nav-link' href="{{ backpack_url('attendance') }}"><i class="nav-icon la la-calendar"></i> <span>@lang('Attendance')</span></a></li>
        <li class='nav-item'><a class='nav-link' href="{{ backpack_url('result') }}"><i class="nav-icon la la-percent"></i> <span>@lang('Results')</span></a></li>
        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('member') }}'><i class='nav-icon la la-question'></i>@lang('Members')</a></li>

    <li class="nav-title">{{ Str::upper(trans('Invoicing')) }}</li>
        <li class='nav-item'><a class='nav-link' href="{{ backpack_url('/enrollment?status_id=["1"]&hidechildren=true') }}"><i class="nav-icon la la-credit-card"></i> <span>@lang('Pending')</span></a></li>
        <li class='nav-item'><a class='nav-link' href="{{ backpack_url('/enrollment?scholarship=all&hidechildren=true') }}"><i class="nav-icon la la-comment-dollar"></i> <span>@lang('Scholarships')</span></a></li>
        @if(! config('invoicing.price_categories_enabled'))
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('invoice') }}'><i class='nav-icon la la-dollar'></i> @lang('Invoices')</a></li>
        @endif
        @if (config('invoicing.allow_scheduled_payments'))
            <li class='nav-item'><a class='nav-link' href='{{ backpack_url('scheduled-payment') }}'><i class='nav-icon la la-question'></i> @lang('Scheduled Payments')</a></li>
        @endif

    <li class="nav-title">@lang('CALENDARS')</li>
        <li class='nav-item'><a class='nav-link' href="{{ route('teachersCalendar') }}"><i class="nav-icon la la-binoculars"></i><span>@lang('Teachers overview')</span></a></li>
        <li class='nav-item'><a class='nav-link' href="{{ route('roomsCalendar') }}"><i class="nav-icon la la-binoculars"></i><span>@lang('Rooms overview')</span></a></li>
        @if (isset($teachers) && $teachers->count() > 0)
        <li class="nav-item nav-dropdown"><a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-group"></i> @lang('teachers')</a>
            <ul class="nav-dropdown-items">
              @foreach ($teachers as $teacher)
                <li class='nav-item'><a class='nav-link' href="{{ route('teacherCalendar', ['teacher' => $teacher->id]) }}"><span>{{ $teacher->name }}</span></a></li>
              @endforeach
          </ul>
        </li>
        @endif

        @if(isset($rooms) && $rooms->count() > 0)
        <li class="nav-item nav-dropdown"><a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-building"></i> @lang('rooms')</a>
            <ul class="nav-dropdown-items">
              @foreach ($rooms as $room)
                <li class='nav-item'><a class='nav-link' href="{{ route('roomCalendar', ['room' => $room->id]) }}"><span>{{ $room->name }}</span></a></li>
              @endforeach
            </ul>
        </li>
        @endif

        <li class='nav-item'><a class='nav-link' href="{{ backpack_url('event') }}"><i class="nav-icon la la-calendar"></i> <span>@lang('Events')</span></a></li>
@endif


@if(backpack_user()->hasRole('admin'))
    <li class="nav-title">@lang('HR')</li>
        <li class='nav-item'><a class='nav-link' href="{{ backpack_url('teacher') }}"><i class="nav-icon la la-users"></i> <span>@lang('Teachers')</span></a></li>
        <li class='nav-item'><a class='nav-link' href="{{ route('hrDashboard') }}"><i class="nav-icon la la-clock"></i> <span>@lang('Hours')</span></a></li>
        <li class='nav-item'><a class='nav-link' href="{{ backpack_url('leave/teachers') }}"><i class="nav-icon la la-calendar"></i> <span>@lang('Leave')</span></a></li>
@endif


@if(backpack_user()->hasRole(['admin', 'manager']) || backpack_user()->hasPermissionTo('reports.view'))

    <li class="nav-title">@lang('REPORTS')</li>

        <li class="nav-item nav-dropdown"><a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-link"></i> @lang('Internal Courses')</a>
        <ul class="nav-dropdown-items">
            <li class='nav-item'><a class='nav-link' href="{{ route('homeReport') }}"><i class="nav-icon la la-users"></i> <span>@lang('Overview')</span></a></li>
            <li class='nav-item'><a class='nav-link' href="{{ route('rhythmReport') }}"><i class="nav-icon la la-pie-chart"></i><span>@lang('Per rhythm')</span></a></li>
            <li class='nav-item'><a class='nav-link' href="{{ route('levelReport') }}"><i class="nav-icon la la-pie-chart"></i><span>@lang('Per level')</span></a></li>
            <li class='nav-item'><a class='nav-link' href="{{ route('courseReport') }}"><i class="nav-icon la la-graduation-cap"></i><span>@lang('Per course')</span></a></li>
            <li class='nav-item'><a class='nav-link' href="{{ route('genderReport') }}"><i class="nav-icon la la-graduation-cap"></i><span>@lang('Per gender')</span></a></li>
        </ul></li>

        @if (config('settings.external_courses_enabled'))
        <li class="nav-item nav-dropdown"><a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-link"></i> @lang('External Courses')</a>
        <ul class="nav-dropdown-items">
            <li class='nav-item'><a class='nav-link' href="{{ route('externalReport') }}"><i class="nav-icon la la-bus"></i> <span>@lang('Per period')</span></a></li>
            <li class='nav-item'><a class='nav-link' href="{{ route('externalReport2') }}"><i class="nav-icon la la-bus"></i> <span>@lang('Per date')</span></a></li>
            <li class='nav-item'><a class='nav-link' href="{{ route('externalReport3') }}"><i class="nav-icon la la-bus"></i> <span>@lang('Per institution')</span></a></li>
        </ul></li>
        @endif

@endif

@if(backpack_user()->hasRole(['admin', 'secretary']))
    <li class="nav-title"></li>

    <li class="nav-item nav-dropdown"><a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-link"></i> @lang('Settings')</a>
        <ul class="nav-dropdown-items">
            <li class="nav-item nav-dropdown"><a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-link"></i> @lang('Invoicing')</a>
                <ul class="nav-dropdown-items">
                    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('discount') }}'><i class='la la-percent'></i> <span>@lang('Discounts')</span></a></li>
                    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('fee') }}'><i class='la la-dollar'></i> <span>@lang('Fees')</span></a></li>
                    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('paymentmethod') }}'><i class='la la-dollar'></i> <span>@lang('Payment methods')</span></a></li>
                    <li class='nav-item'><a class='nav-link' href='{{ backpack_url('tax') }}'><i class='nav-icon la la-question'></i> @lang('Taxes')</a></li>
                </ul>
            </li>

            @if(backpack_user()->hasRole('admin'))
                <li class="nav-item nav-dropdown"><a class="nav-link nav-dropdown-toggle" href="#"><i class="nav-icon la la-link"></i> @lang('Administration')</a>
                    <ul class="nav-dropdown-items">
                        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('institution') }}'><i class='nav-icon la la-building'></i> @lang('Institutions')</a></li>
                        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('profession') }}'><i class='nav-icon la la-question'></i> @lang('Professions')</a></li>
                        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('partner') }}'><i class='nav-icon la la-building'></i> @lang('Partnerships')</a></li>
                        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('scholarship') }}'><i class='nav-icon la la-comment-dollar'></i> @lang('Scholarships')</a></li>
                        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('period') }}'><i class='la la-calendar'></i> <span>@lang('periods')</span></a></li>
                        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('level') }}'><i class='la la-star'></i> <span>@lang('levels')</span></a></li>
                        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('room') }}'><i class='la la-star'></i> <span>@lang('rooms')</span></a></li>
                        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('campus') }}'><i class='la la-star'></i> <span>@lang('campuses')</span></a></li>
                        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('rhythm') }}'><i class='la la-star'></i> <span>@lang('rhythms')</span></a></li>
                        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('schedulepreset') }}'><i class='nav-icon la la-question'></i> Schedule Presets</a></li>
                        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('year') }}'><i class='la la-star'></i> <span>@lang('Years')</span></a></li>
                        <li class='nav-item'><a class='nav-link' href='{{ backpack_url('book') }}'><i class='la la-book'></i> <span>@lang('Books')</span></a></li>
                    </ul>

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
        </ul>
@endif


@if(backpack_user()->isTeacher())
    <li class="nav-title">@lang('TEACHER')</li>
        <li class='nav-item'><a class='nav-link' href="{{ route('teacherCalendar', ['teacher' => backpack_user()->id]) }}"><i class="nav-icon la la-calendar"></i> <span>@lang('My Schedule')</span></a></li>
@endif

@if(backpack_user()->isStudent())
    <li class="nav-item"><a class='nav-link' href="{{ route('backpack.account.edit_info') }}"><i class="la la-user"></i> <span>{{ trans('backpack::base.my_account') }}</span></a></li>
@endif

