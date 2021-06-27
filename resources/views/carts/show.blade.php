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
            :productslist="{{ json_encode($products) }}"
            :clients="{{ json_encode($clients) }}"
            :invoicetypes="{{ json_encode($invoicetypes) }}"
            :availablebooks="{{ json_encode($availableBooks) }}"
            :availablefees="{{ json_encode($availableFees) }}"
            :availablediscounts="{{ json_encode($availableDiscounts) }}"
            :availabletaxes="{{ json_encode($availableTaxes) }}"
            :availablepaymentmethods="{{ json_encode($availablePaymentMethods) }}"
            :accountingenabled="{{ json_encode(config('invoicing.accounting_enabled')) }}"
            currency="{{ config('app.currency_symbol') }}"
            currencyposition="{{ config('app.currency_position') }}"
            :scheduledpayments="{{ json_encode(config('invoicing.allow_scheduled_payments')) }}"
        >
        </cart-component>

</div>

@endsection

@section('after_scripts')
        <script src="{{ mix('/js/app.js') }}"></script>
    <script src="{{ mix('/js/manifest.js') }}"></script>
    <script src="{{ mix('/js/vendor.js') }}"></script>
@endsection
