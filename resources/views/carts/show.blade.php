@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        @lang('Cart Details')
    </h2>
</section>
@endsection


@section('content')


<div id="app">

    <cart-component
        :enrollment="{{ json_encode($enrollment) }}"
        :contactdata="{{ json_encode($contactData) }}"
        :availablepaymentmethods="{{ json_encode($availablePaymentMethods) }}"
        :previouspayments="{{ json_encode($previouspayments) }}"
    ></cart-component>

</div>

@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection


<!-- include field specific select2 js-->
@push('crud_fields_scripts')
<script>
    jQuery(document).ready($ => $('.js-example-basic-single').select2());
</script>
@endpush
