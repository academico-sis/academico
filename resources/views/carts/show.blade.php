@extends('backpack::blank')

@section('header')
<section class="content-header">
    <h1>
        @lang('Cart Details')
    </h1>
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

    {{-- FIELD CSS - will be loaded in the after_styles section --}}
    @push('crud_fields_styles')
    <!-- include select2 css-->
    <link href="{{ asset('vendor/adminlte/bower_components/select2/dist/css/select2.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2-bootstrap-theme/0.1.0-beta.10/select2-bootstrap.min.css" rel="stylesheet" type="text/css" />
    @endpush

    {{-- FIELD JS - will be loaded in the after_scripts section --}}
    @push('crud_fields_scripts')
    <!-- include select2 js-->
    <script src="{{ asset('vendor/adminlte/bower_components/select2/dist/js/select2.min.js') }}"></script>
    <!-- TODO FIXME BP4 NEED TO GET RID OF THIS -->
    @endpush


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