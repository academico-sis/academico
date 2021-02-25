<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                @lang('Invoice') # {{ $invoice->id }}
            </div>

            <div class="card-body">
                <p>
                    @lang('Receipt Number') : {{ $invoice->receipt_number }}
                </p>
                <p>@lang('Date') : {{ $invoice->created_at }}</p>
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
                                <td>${{ $product->price }}</td>
                            </tr>
                        @endforeach
                        <tr style="font-weight: bold">
                            <td>@lang('TOTAL')</td>
                            <td>${{ $invoice->total_price }}</td>
                        </tr>
                    </tbody>
                </table>

                <table class="table">
                    <thead>
                    <th>@lang('Payment')</th>
                    <th>@lang('Value')</th>
                    </thead>
                    <tbody>
                    @foreach($invoice->payments as $payment)
                        <tr>
                            <td>{{ $payment->created_at }}</td>
                            <td>${{ $payment->value }}</td>
                        </tr>
                    @endforeach
                    <tr style="font-weight: bold">
                        <td>@lang('TOTAL')</td>
                        <td>${{ $invoice->paidTotal() }}</td>
                    </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

</div>

