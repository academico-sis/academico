@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>@lang('View Invoice')</h2>
</section>
@endsection


@section('content')

<div class="row">
    <div class="col-md-12">
        @include('invoices.details')
    </div>
</div>
@endsection
