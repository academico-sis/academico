@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>@lang('View Invoice')</h2>
</section>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-4">

            <student-comments
                    :comments="{{ json_encode($comments) }}"
                    :id="{{ json_encode($invoice->id) }}"
                    :type="'App\\Models\\Invoice'"
                    route="{{ route('storeComment') }}">
            </student-comments>

            <div class="card">
                <div class="card-header">
                    @lang('Invoice') # {{ $invoice->invoice_reference }}
                    <div class="card-header-actions">
                        <a class="btn btn-sm btn-warning" href="{{ route('invoice.edit', ['id' => $invoice->id]) }}">
                            {{ __('Edit') }}
                        </a>

                        @include('partials.delete-button', ['route' => "/invoice/$invoice->id", 'redirectRoute' => $afterSuccessUrl])
                    </div>
                </div>

                <div class="card-body">
                    <p>@lang('Date') : {{ $invoice->formatted_date }}</p>
                    @if ($enrollment) <p>@lang('Enrollment for') : <a href="{{ route('enrollment.show', ['id' => $enrollment->id]) }}">{{ $enrollment->student->firstname }} {{ $enrollment->student->lastname }}</a></p>@endif
                    <p>@lang('Client name') : {{ $invoice->client_name }}</p>
                    <p>@lang('Client email') : {{ $invoice->client_email }}</p>
                    <p>@lang('Client address') : {{ $invoice->client_address }}</p>
                    <p>@lang('Client ID Number') : {{ $invoice->client_idnumber }}</p>
                </div>
            </div>
        </div>


        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    @lang('Details')
                    <div class="card-header-actions">
                        <a href="{{ route('export-invoice', ['invoice' => $invoice]) }}">{{ __('Export') }}</a>
                    </div>
                </div>

                <div class="card-body">

                    <table class="table">
                        <thead>
                            <th>@lang('Product')</th>
                            <th>@lang('Price')</th>
                        </thead>
                        <tbody>
                        @foreach($invoice->invoiceDetails as $product)
                            <tr>
                                <td>
                                    {{ $product->product_name }}
                                    @if ($product->comment)<p style="font-style: italic">{{ $product->comment }}</p>@endif
                                </td>
                                <td>
                                    {{ $product->quantity }} x {{ $product->price_with_currency }}
                                    @if ($product->tax_rate > 0)
                                        <span class="badge badge-secondary">{{ __('Tax:') }} {{ $product->tax_rate }}%</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        <tr style="font-weight: bold">
                            <td>@lang('TOTAL')</td>
                            <td>{{ $invoice->total_price_with_currency }}</td>
                        </tr>
                        </tbody>
                    </table>

                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    @lang('Payments')
                    <div class="card-header-actions">
                        <a class="btn btn-sm btn-warning" href="{{ route('invoice.edit', ['id' => $invoice->id]) }}">
                            {{ __('Edit') }}
                        </a>
                    </div>
                </div>

                <div class="card-body">

                    <table class="table">
                        <thead>
                        <tr>
                            <th>{{ __("Payment") }}</th>
                            <th>{{ __("Date") }}</th>
                            <th>{{ __("Value") }}</th>
                            <th>{{ __('Comment') }}</th>
                            <th></th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($invoice->payments as $payment)
                            <tr>
                                <td >{{ $payment->payment_method }}</td>

                                <td>
                                    {{ $payment->date_for_humans }}
                                </td>

                                <td>
                                    @if (config('academico.currency_position') === 'before')
                                        {{ config('academico.currency_symbol') }} {{ $payment->value }}
                                    @else
                                        {{ $payment->value }} {{ config('academico.currency_symbol') }}
                                    @endif
                                </td>

                                <td>
                                    {{ $payment->comment }}
                                </td>

                            </tr>
                        @endforeach
                        <tr>
                            <td></td>
                            <td style="font-weight: bold">{{ __("Total received amount") }}</td>
                            <td style="font-weight: bold">
                                @if (config('academico.currency_position') === 'before')
                                    {{ config('academico.currency_symbol') }} {{ $paidTotal }}
                                @else
                                    {{ $paidTotal }} {{ config('academico.currency_symbol') }}
                                @endif
                            </td>
                            <td></td>
                            <td></td>
                        </tr>

                        </tbody>
                    </table>

                </div>
            </div>
        </div>

    </div>

@endsection
