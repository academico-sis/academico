@extends('backpack::blank')

@section('header')
    <section class="container-fluid">
        <h2>
            @lang('Create Scheduled Payments')
        </h2>
    </section>
@endsection


@section('content')
<div class="row" id="app">
    <div class="col-md-12">
        <div class="card">
            <cart-scheduled-payments-component
                v-if="{{ config('invoicing.allow_scheduled_payments') }}"
                currency="{{ config('app.currency_symbol') }}"
                currencyposition="{{ config('app.currency_position') }}"
                :enrollment="{{ json_encode($enrollment) }}"
            ></cart-scheduled-payments-component>
        </div>
    </div>

</div>

@endsection

@section('after_scripts')
    <script src="{{ mix('/js/app.js') }}"></script>
    <script src="{{ mix('/js/manifest.js') }}"></script>
    <script src="{{ mix('/js/vendor.js') }}"></script>
@endsection
