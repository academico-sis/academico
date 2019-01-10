<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li><a href="{{ url('/') }}"><i class="fa fa-book"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>

<li><a href="{{ backpack_url('course') }}"><i class="fa fa-book"></i> <span>@lang_u('academico.courses')</span></a></li>

<li><a href="{{ backpack_url('student') }}"><i class="fa fa-user"></i> <span>@lang_u('academico.students')</span></a></li>

<li><a href="{{ backpack_url('/enrollment?pending=true') }}"><i class="fa fa-credit-card"></i> <span>@lang_u('academico.pending')</span></a></li>

<li><a href="{{ backpack_url('preinvoice') }}"><i class="fa fa-dollar"></i> <span>@lang_u('academico.invoices')</span></a></li>

<li><a href="{{ backpack_url('comment') }}"><i class="fa fa-pencil"></i> <span>@lang_u('academico.comment')</span></a></li>


<li class="treeview">
  <a href="#"><i class="fa fa-link"></i> <span>@lang_u('academico.settings')</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
  <ul class="treeview-menu">
      <li><a href='{{ backpack_url('period') }}'><i class='fa fa-calendar'></i> <span>@lang_u('academico.periods')</span></a></li>
      <li><a href='{{ backpack_url('event') }}'><i class='fa fa-star'></i> <span>@lang_u('academico.events')</span></a></li>
      <li><a href='{{ backpack_url('level') }}'><i class='fa fa-star'></i> <span>@lang_u('academico.levels')</span></a></li>
      <li><a href='{{ backpack_url('room') }}'><i class='fa fa-star'></i> <span>@lang_u('academico.rooms')</span></a></li>
      <li><a href='{{ backpack_url('campus') }}'><i class='fa fa-star'></i> <span>@lang_u('academico.campuses')<an></a></li>
      <li><a href='{{ backpack_url('rythm') }}'><i class='fa fa-star'></i> <span>@lang_u('academico.rythms')</span></a></li>
      <li><a href='{{ backpack_url('year') }}'><i class='fa fa-star'></i> <span>@lang_u('academico.years')</span></a></li>
  </ul>
</li>

{{-- EVALUATION METHODS --}}
<li class="treeview">
  <a href="#"><i class="fa fa-link"></i> <span>@lang_u('academico.evaluation')</span><span class="pull-right-container"><i class="fa fa-angle-left pull-right"></i></span></a>
  <ul class="treeview-menu">
      <li><a href='{{ backpack_url('evaluationtype') }}'><span>@lang_u('academico.evaluation_types')</span></a></li>
      <li><a href='{{ backpack_url('gradetype') }}'><span>@lang_u('academico.grade_types')</span></a></li>
      <li><a href='{{ backpack_url('skill') }}'><span>@lang_u('academico.skills')</span></a></li>
      <li><a href='{{ backpack_url('skilltype') }}'><span>@lang_u('academico.skill_types')</span></a></li>
      <li><a href='{{ backpack_url('skillscale') }}'><span>@lang_u('academico.skill_scales')</span></a></li>
      <li><a href='{{ backpack_url('resulttype') }}'><span>@lang_u('academico.result_types')</span></a></li>

  </ul>
</li>

<!-- Users, Roles Permissions -->
<li class="treeview">
  <a href="#"><i class="fa fa-group"></i> <span>@lang_u('academico.users')</span> <i class="fa fa-angle-left pull-right"></i></a>
  <ul class="treeview-menu">
    <li><a href="{{ backpack_url('user') }}"><i class="fa fa-user"></i> <span>@lang_u('academico.users')</span></a></li>
    <li><a href="{{ backpack_url('role') }}"><i class="fa fa-group"></i> <span>@lang_u('academico.roles')</span></a></li>
    <li><a href="{{ backpack_url('permission') }}"><i class="fa fa-key"></i> <span>@lang_u('academico.permissions')</span></a></li>
  </ul>
</li>
