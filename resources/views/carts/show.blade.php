@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        @lang('Cart Details')
    </h2>
</section>
@endsection


@section('content')


<div>

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
        :allowemptypaymentmethods="{{ json_encode(config('invoicing.allow_empty_payment_methods')) }}"
        :allowedblankfields="{{ json_encode(config('invoicing.allowed_blank_fields')) }}"
        currency="{{ config('app.currency_symbol') }}"
        currencyposition="{{ config('app.currency_position') }}"
    >
    </cart-component>

</div>

@endsection