<div class="col-md-6">
    <div class="card">
        <div class="card-header">@lang('Scheduled Payments')
            <div class="card-header-actions">
                @if ($enrollment->scheduledPayments->count() === 0)
                    <a class="btn btn-sm btn-warning" href="{{ route('enrollment-scheduled-payments', ['enrollment' => $enrollment->id]) }}">
                        {{ __('Create') }}
                    </a>
                @else
                    <a class="btn btn-sm btn-warning" href="{{ route('enrollment.edit', ['id' => $enrollment->id]) }}">
                        {{ __('Edit') }}
                    </a>
                @endif
            </div>
        </div>

        <div class="card-body">

            <ul>
                @foreach($enrollment->scheduledPayments as $scheduledPayment)
                    <li>
                        {{ $scheduledPayment->date_for_humans }} &middot; {{ $scheduledPayment->value_with_currency }} &middot;
                        @if($scheduledPayment->computedStatus() === 2)
                            <span class="badge badge-primary">{{ __('Paid') }}</span>
                        @else
                            <span class="badge badge-warning">{{ __('Pending') }}</span>
                        @endif
                        &middot;
                            @forelse ($scheduledPayment->invoices as $invoice)
                                <a href="{{ route('invoice.show', ['id' => $invoice->id]) }}">{{ $invoice->invoice_reference }} - {{ $invoice->formatted_date }}</a>
                            @empty
                                <a href="{{ route('checkout-scheduled-payment', ['scheduledPayment' => $scheduledPayment->id]) }}">{{__('Checkout')}}</a>
                            @endforelse
                    </li>
                @endforeach
            </ul>

        </div>
    </div>
</div>
