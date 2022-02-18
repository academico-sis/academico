@extends('backpack::blank')

@section('header')
    <section class="container-fluid">
        <h2>
            @lang('Create Scheduled Payments')
        </h2>
    </section>
@endsection


@section('content')
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <cart-scheduled-payments-component
                v-if="{{ config('invoicing.allow_scheduled_payments') }}"
                currency="{{ config('academico.currency_symbol') }}"
                currencyposition="{{ config('academico.currency_position') }}"
                :enrollment="{{ json_encode($enrollment) }}"
            ></cart-scheduled-payments-component>
        </div>
    </div>

</div>

@endsection
