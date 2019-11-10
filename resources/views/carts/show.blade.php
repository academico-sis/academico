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
            :enrollmentslist="{{ json_encode($enrollments) }}"
            :bookslist="{{ json_encode($books) }}"
            :feeslist="{{ json_encode($fees) }}"
            :availablebooks="{{ json_encode($availableBooks) }}"
            :availablefees="{{ json_encode($availableFees) }}"
            :availablediscounts="{{ json_encode($availableDiscounts) }}"
            :contactdata="{{ json_encode($contactData) }}"
            :availablepaymentmethods="{{ json_encode($availablePaymentMethods) }}"

        >
        </cart-component>

</div>

@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection


<!-- include field specific select2 js-->
@push('crud_fields_scripts')
<script>
    jQuery(document).ready(function($) {
        $('.js-example-basic-single').select2();
    });
</script>
@endpush