@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>@lang('View Invoice')</h2>
</section>
@endsection


@section('content')
    <div class="row">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header">
                    @lang('Invoice') # {{ $invoice->invoice_reference }}
                    <div class="card-header-actions">
                        <a class="btn btn-sm btn-warning" href="{{ route('invoice.edit', ['id' => $invoice->id]) }}">
                            {{ __('Edit') }}
                        </a>

                            <a href="javascript:void(0)" onclick="deleteEntry(this)" data-route="{{ url('invoice/'.$invoice->id) }}" class="btn btn-sm btn-link" data-button-type="delete"><i class="la la-trash"></i></a>

                        {{-- Button Javascript --}}
                        {{-- - used right away in AJAX operations (ex: List) --}}
                        {{-- - pushed to the end of the page, after jQuery is loaded, for non-AJAX operations (ex: Show) --}}
                        @push('after_scripts') @if (request()->ajax()) @endpush @endif
                        <script>

                            if (typeof deleteEntry != 'function') {
                                $("[data-button-type=delete]").unbind('click');

                                function deleteEntry(button) {
                                    // ask for confirmation before deleting an item
                                    // e.preventDefault();
                                    var route = $(button).attr('data-route');

                                    swal({
                                        title: "{!! trans('backpack::base.warning') !!}",
                                        text: "{!! trans('backpack::crud.delete_confirm') !!}",
                                        icon: "warning",
                                        buttons: ["{!! trans('backpack::crud.cancel') !!}", "{!! trans('backpack::crud.delete') !!}"],
                                        dangerMode: true,
                                    }).then((value) => {
                                        if (value) {
                                            $.ajax({
                                                url: route,
                                                type: 'DELETE',
                                                success: function(result) {
                                                    if (result == 1) {
                                                        // Redraw the table
                                                        if (typeof crud != 'undefined' && typeof crud.table != 'undefined') {
                                                            crud.table.draw(false);
                                                        }

                                                        // Show a success notification bubble
                                                        new Noty({
                                                            type: "success",
                                                            text: "{!! '<strong>'.trans('backpack::crud.delete_confirmation_title').'</strong><br>'.trans('backpack::crud.delete_confirmation_message') !!}"
                                                        }).show();

                                                        // Hide the modal, if any
                                                        $('.modal').modal('hide');

                                                        window.location=document.referrer;
                                                    } else {
                                                        // if the result is an array, it means
                                                        // we have notification bubbles to show
                                                        if (result instanceof Object) {
                                                            // trigger one or more bubble notifications
                                                            Object.entries(result).forEach(function(entry, index) {
                                                                var type = entry[0];
                                                                entry[1].forEach(function(message, i) {
                                                                    new Noty({
                                                                        type: type,
                                                                        text: message
                                                                    }).show();
                                                                });
                                                            });
                                                        } else {// Show an error alert
                                                            swal({
                                                                title: "{!! trans('backpack::crud.delete_confirmation_not_title') !!}",
                                                                text: "{!! trans('backpack::crud.delete_confirmation_not_message') !!}",
                                                                icon: "error",
                                                                timer: 4000,
                                                                buttons: false,
                                                            });
                                                        }
                                                    }
                                                },
                                                error: function(result) {
                                                    // Show an alert with the result
                                                    swal({
                                                        title: "{!! trans('backpack::crud.delete_confirmation_not_title') !!}",
                                                        text: "{!! trans('backpack::crud.delete_confirmation_not_message') !!}",
                                                        icon: "error",
                                                        timer: 4000,
                                                        buttons: false,
                                                    });
                                                }
                                            });
                                        }
                                    });

                                }
                            }

                            // make it so that the function above is run after each DataTable draw event
                            // crud.addFunctionToDataTablesDrawEventQueue('deleteEntry');
                        </script>
                        @if (!request()->ajax()) @endpush @endif

                    </div>
                </div>

                <div class="card-body">
                    <p>@lang('Date') : {{ $invoice->formatted_date }}</p>
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
                    <div class="card-header-actions"><a href="{{ route('export-invoice', ['invoice' => $invoice]) }}">{{ __('Export') }}</a></div>
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
                                <td>{{ $product->product_name }}</td>
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

                    <payment-component
                        :invoice="{{ json_encode($invoice) }}"
                        :availablepaymentmethods="{{ json_encode($availablePaymentMethods ?? []) }}"
                        editable="{{ $editable ?? false }}"
                        currency="{{ config('app.currency_symbol') }}"
                        currencyposition="{{ config('app.currency_position') }}"
                        enrollment-id="{{ $invoice->enrollments->first()->id }}"
                    ></payment-component>

                </div>
            </div>
        </div>

    </div>

@endsection
