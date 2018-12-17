<!-- This file is used to store sidebar items, starting with Backpack\Base 0.9.0 -->
<li><a href="{{ url('/') }}"><i class="fa fa-book"></i> <span>{{ trans('backpack::base.dashboard') }}</span></a></li>

<li><a href="{{ url('/courses') }}"><i class="fa fa-book"></i> <span>@lang_u('academico.courses')
</span></a></li>

<li class="treeview">
  <a href="#"><i class="fa fa-link"></i> <span>@lang_u('academico.settings')</span>
        <span class="pull-right-container">
          <i class="fa fa-angle-left pull-right"></i>
        </span>
      </a>
  <ul class="treeview-menu">
      <li><a href='{{ backpack_url('period') }}'><i class='fa fa-calendar'></i> <span>@lang_u('academico.periods')</span></a></li>
      <li><a href='{{ backpack_url('course') }}'><i class='fa fa-graduation-cap'></i> <span>@lang_u('academico.courses')</span></a></li>
      <li><a href='{{ backpack_url('event') }}'><i class='fa fa-star'></i> <span>@lang_u('academico.events')</span></a></li>
      <li><a href='{{ backpack_url('level') }}'><i class='fa fa-star'></i> <span>@lang_u('academico.levels')</span></a></li>
      <li><a href='{{ backpack_url('room') }}'><i class='fa fa-star'></i> <span>@lang_u('academico.rooms')</span></a></li>
      <li><a href='{{ backpack_url('campus') }}'><i class='fa fa-star'></i> <span>@lang_u('academico.campuses')</span></a></li>
      <li><a href='{{ backpack_url('rythm') }}'><i class='fa fa-star'></i> <span>@lang_u('academico.rythms')</span></a></li>
      <li><a href='{{ backpack_url('year') }}'><i class='fa fa-star'></i> <span>@lang_u('academico.years')</span></a></li>
  </ul>
</li>

<li><a href='{{ backpack_url('user') }}'><i class='fa fa-user'></i> <span>@lang_u('academico.users')</span></a></li>