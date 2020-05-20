<!-- This file is used to store topbar (right) items -->


{{-- <li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="la la-bell"></i><span class="badge badge-pill badge-danger">5</span></a></li>
<li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="la la-list"></i></a></li>
<li class="nav-item d-md-down-none"><a class="nav-link" href="#"><i class="la la-map"></i></a></li> --}}

@foreach (language()->allowed() as $code => $name)
<li class="nav-item px-3">
    <a class="nav-link" href="{{ language()->back($code) }}">{{ $name }}</a>
</li>
@endforeach