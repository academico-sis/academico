@extends(backpack_user() && (starts_with(\Request::path(), config('backpack.base.route_prefix'))) ? 'backpack::layout' : 'backpack::layout_guest')
{{-- show error using sidebar layout if looged in AND on an admin page; otherwise use a blank page --}}

@php
  $title = 'Error '.$error_number;
@endphp

@section('after_styles')
  <style>
    .error_number {
      font-size: 156px;
      font-weight: 600;
      color: #dd4b39;
      line-height: 100px;
    }
    .error_number small {
      font-size: 56px;
      font-weight: 700;
    }

    .error_number hr {
      margin-top: 60px;
      margin-bottom: 0;
      border-top: 5px solid #dd4b39;
      width: 50px;
    }

    .error_title {
      margin-top: 40px;
      font-size: 36px;
      color: #B0BEC5;
      font-weight: 400;
    }

    .error_description {
      font-size: 24px;
      color: #B0BEC5;
      font-weight: 400;
    }
  </style>
@endsection

@section('content')
<div class="row">
  <div class="col-md-12 text-center">
    <div class="error_number m-t-80">
      <small>ERROR</small><br>
      {{ $error_number }}
      <hr>
    </div>
    <div class="error_title">
      @yield('title')
    </div>
    <div class="error_description">
      <small>
        @yield('description')
     </small>
    </div>
  </div>
</div>
@if(app()->bound('sentry') && app('sentry')->getLastEventId())
        <script src="https://browser.sentry-cdn.com/5.7.1/bundle.min.js" integrity="sha384-KMv6bBTABABhv0NI+rVWly6PIRvdippFEgjpKyxUcpEmDWZTkDOiueL5xW+cztZZ" crossorigin="anonymous"></script>
        <script>
            Sentry.init({ dsn: 'https://5ab055b8100145968e5b2993527cfa29@sentry.io/1355049' });
            Sentry.showReportDialog({
				eventId: '{{ Sentry::getLastEventID() }}',
				lang: 'es',
			});
        </script>
    @endif
@endsection