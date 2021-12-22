@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        @lang('Enrollment Details')
    </h2>
</section>
@endsection


@section('content')

<div>
<div class="row">

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">@lang('Enrollment Info')
                <div class="card-header-actions">
                    <a class="btn btn-sm btn-warning" href="{{ route('enrollment.edit', ['id' => $enrollment->id]) }}">
                        {{ __('Edit') }}
                    </a>

                    <a class="btn btn-sm btn-warning" href="{{ route('get-courses-list', ['mode' => 'update', 'enrollment_id' => $enrollment->id]) }}">@lang('Change course')</a>

                    @include('partials.delete-button', ['route' => "/enrollment/$enrollment->id", 'redirectRoute' => "/student/$enrollment->student_id/show"])

                </div>
            </div>

            <div class="card-body">
                <p>@lang('name'):
                    <a href="{{ url('student') }}/{{$enrollment->student->id }}/show">{{ $enrollment->student->firstname }} {{ $enrollment->student->lastname }}</a>
                </p>
                <p>@lang('Enrollment date'): {{ $enrollment->date }}</p>
                <p>@lang('Enrollment ID'): {{ $enrollment->id }}</p>
                <p>@lang('Course'): <a href="{{ route('enrollment.index', ['course_id' => $enrollment->course_id]) }}">{{ $enrollment->course->name }}</a></p>
                <p>@lang('Period'): {{ $enrollment->course->period->name }}</p>
                <p><enrollment-price-field writeaccess="{{ $writeaccess }}" :enrollment="{{ json_encode($enrollment) }}" currency="{{ config('app.currency_symbol') }}" currencyposition="{{ config('app.currency_position') }}"></enrollment-price-field></p>

                <div class="form-group">
                    @if($enrollment->status_id === 2)
                        <div class="badge badge-primary">{{ $enrollment->status }}</div>
                    @elseif($enrollment->status_id === 1)
                        <div class="badge badge-warning">{{ $enrollment->status }}</div>
                    @else
                        {{ $enrollment->status }}
                    @endif
                </div>

                <div class="form-group">
                    @foreach ($enrollment->scholarships as $scholarship)
                        {{ $scholarship->name }} (<a href="#" onclick="if(confirm('Voulez-vous vraiment retirer cette bourse ?')) removeScholarship({{ $enrollment->id }}, {{ $scholarship->id }})">{{ __('Cancel') }}</a>)
                    @endforeach
                </div>

                @if(backpack_user()->can('enrollments.edit'))
                    <scholarship-modal-component enrollment_id="{{ $enrollment->id }}" :scholarships="{{ $scholarships }}"></scholarship-modal-component>
                @endif

                @if ($enrollment->children_count > 0)
                    <p>@lang('Children enrollments'):</p>
                    <ul>
                    @foreach ($enrollment->children as $children)
                        <li>
                            <a href="/enrollment/{{ $children->id }}/show">{{ $children->course->name }}</a> ({{ $children->course->period->name }})
                        </li>
                    @endforeach
                    </ul>
                @elseif ($enrollment->parent_id !== null)
                    @lang('This enrollment belongs to') <a href="/enrollment/{{ $enrollment->parent_id }}/show">{{ $enrollment->parent_id }}</a>
                @endif

                @if(isset($enrollment->result))
                    <p>
                        <a href="/result/{{ $enrollment->id }}/show" class="btn btn-sm btn-info">
                            <i class="la la-eye"></i> {{ $enrollment->result['result_name']['name'] }}
                        </a>
                    </p>
                @endif

                @if(config('enrollment-sheet.supported'))
                    <a href="{{ route('generate-enrollment-sheet', ['enrollment' => $enrollment]) }}">{{ __('Enrollment sheet') }} (.docx)</a>
                @endif
            </div>
        </div>
    </div>


    <div class="col-md-6">

        <student-comments
            :comments="{{ json_encode($comments) }}"
            :id="{{ json_encode($enrollment->id) }}"
            :type="'App\\Models\\Enrollment'"
            route="{{ route('storeComment') }}">
        </student-comments>

    </div>

</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                @lang('Invoicing')
                <div class="card-header-actions">
                    @if(backpack_user()->can('enrollments.edit') && $enrollment->parent_id == null)
                        <a href="/enrollment/{{ $enrollment->id }}/bill" class="btn btn-primary btn-sm">@lang('Checkout enrollment')</a>
                    @endif
                </div>
            </div>

            <div class="card-body">

                <table class="table">
                    <thead>
                        <tr>
                            <th>@lang('Date')</th>
                            <th>@lang('Invoice') #</th>
                            <th>@lang('Value')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($enrollment->invoices as $invoice)
                            <tr>
                                <td>
                                    @if (!$invoice->invoice_reference)
                                        <a href="{{ route('invoice.show', ['id' => $invoice->id]) }}">{{ $invoice->formatted_date }}</a>
                                    @else
                                        {{ $invoice->formatted_date }}
                                    @endif
                                </td>
                                <td><a href="{{ route('invoice.show', ['id' => $invoice->id]) }}">{{ $invoice->invoice_reference }}</a></td>
                                <td>{{ $invoice->total_price_with_currency }}</td>
                            </tr>

                                @foreach($invoice->payments as $payment)
                                    <tr style="font-size: small; font-style: italic;">
                                        <td>Paiement du {{ $payment->date_for_humans }}</td>
                                        <td></td>
                                        <td>
                                            {{ $payment->value_with_currency }}
                                        </td>
                                    </tr>
                                @endforeach
                        @empty
                            <tr>
                                <td>Pas encore de facture pour cette inscription.</td>
                            </tr>
                        @endforelse

                        <tr>
                            <td><strong>{{ __('Balance') }}</strong></td>
                            <td></td>
                            <td>
                                @if (config('app.currency_position') === 'before')
                                    {{ config('app.currency_symbol') }} <strong>{{ $enrollment->balance }}</strong>
                                @else
                                    <strong>{{ $enrollment->balance }}</strong> {{ config('app.currency_symbol') }}
                                @endif
                            </td>
                        </tr>
                    </tbody>
                </table>

            </div>
        </div>
    </div>

    @if(config('invoicing.allow_scheduled_payments'))
        @include('enrollments.scheduled-payments')
    @endif

</div>


</div>

@endsection

@section('before_scripts')

<script>
        function removeScholarship(enrollment, scholarship)
        {
            axios
            .post(`/enrollment/${enrollment}/scholarships/remove`, {
                scholarship_id: scholarship
            })
            .then(function (response) {
                window.location.href=`/enrollment/${enrollment}/show`
            })
            .catch(function (error) {
                console.log(error);
            });
        }
</script>

@endsection
