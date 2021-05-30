@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>@lang('View Invoice')</h2>
</section>
@endsection


@section('content')

<div class="row" id="app">
    <div class="col-md-12">
        @include('invoices.details')
    </div>
</div>
@endsection

@section('after_scripts')
    <script src="{{ mix('/js/app.js') }}"></script>
    <script src="{{ mix('/js/manifest.js') }}"></script>
    <script src="{{ mix('/js/vendor.js') }}"></script>
@endsection
