<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li><a href="{{ url('/') }}"><i class="fa fa-book"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>

<li><a href="{{ backpack_url('course') }}"><i class="fa fa-book"></i> <span>@lang('courses')</span></a></li>

<li><a href="{{ backpack_url('student') }}"><i class="fa fa-user"></i> <span>@lang('students')</span></a></li>

<li><a href="{{ backpack_url('/enrollment?pending=true') }}"><i class="fa fa-credit-card"></i> <span>@lang('pending')</span></a></li>

<li><a href="{{ backpack_url('preinvoice') }}"><i class="fa fa-dollar"></i> <span>@lang('invoices')</span></a></li>

<li><a href="{{ backpack_url('comment') }}"><i class="fa fa-pencil"></i> <span>@lang('comment')</span></a></li>


<li class="treeview">
  <a href="#"><i class="fa fa-link"></i> <span>@lang('settings')</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
  <ul class="treeview-menu">
      <li><a href='{{ backpack_url('period') }}'><i class='fa fa-calendar'></i> <span>@lang('periods')</span></a></li>
      <li><a href='{{ backpack_url('event') }}'><i class='fa fa-star'></i> <span>@lang('events')</span></a></li>
      <li><a href='{{ backpack_url('level') }}'><i class='fa fa-star'></i> <span>@lang('levels')</span></a></li>
      <li><a href='{{ backpack_url('room') }}'><i class='fa fa-star'></i> <span>@lang('rooms')</span></a></li>
      <li><a href='{{ backpack_url('campus') }}'><i class='fa fa-star'></i> <span>@lang('campuses')<an></a></li>
      <li><a href='{{ backpack_url('rythm') }}'><i class='fa fa-star'></i> <span>@lang('rythms')</span></a></li>
      <li><a href='{{ backpack_url('year') }}'><i class='fa fa-star'></i> <span>@lang('Years')</span></a></li>
  </ul>
</li>

{{-- EVALUATION METHODS --}}
<li class="treeview">
  <a href="#"><i class="fa fa-link"></i> <span>@lang('evaluation')</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
  <ul class="treeview-menu">
      <li><a href='{{ backpack_url('evaluationtype') }}'><span>@lang('evaluation_types')</span></a></li>
      <li><a href='{{ backpack_url('gradetype') }}'><span>@lang('grade_types')</span></a></li>
      <li><a href='{{ backpack_url('skill') }}'><span>@lang('skills')</span></a></li>
      <li><a href='{{ backpack_url('skilltype') }}'><span>@lang('skill_types')</span></a></li>
      <li><a href='{{ backpack_url('skillscale') }}'><span>@lang('skill_scales')</span></a></li>
      <li><a href='{{ backpack_url('resulttype') }}'><span>@lang('result_types')</span></a></li>

  </ul>
</li>

<!-- Users, Roles Permissions -->
<li class="treeview">
  <a href="#"><i class="fa fa-group"></i> <span>@lang('users')</span> <i class="fa fa-angle-left pull-right"></i></a>
  <ul class="treeview-menu">
    <li><a href="{{ backpack_url('user') }}"><i class="fa fa-user"></i> <span>@lang('users')</span></a></li>
    <li><a href="{{ backpack_url('role') }}"><i class="fa fa-group"></i> <span>@lang('roles')</span></a></li>
    <li><a href="{{ backpack_url('permission') }}"><i class="fa fa-key"></i> <span>@lang('permissions')</span></a></li>
  </ul>
</li>
