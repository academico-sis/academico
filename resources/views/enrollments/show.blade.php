@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        @lang('Enrollment Details')
    </h2>
</section>
@endsection


@section('content')

<div id="app">
<div class="row">

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">@lang('Enrollment Info')
                <div class="card-header-actions">
                    <a class="btn btn-sm btn-warning" href="{{ route('enrollment.edit', ['id' => $enrollment->id]) }}">
                        {{ __('Edit') }}
                    </a>
                </div>
            </div>

            <div class="card-body">
                <p>@lang('name'):
                    <a href="{{ url('student') }}/{{$enrollment->student->id }}/show">{{ $enrollment->student->firstname }} {{ $enrollment->student->lastname }}</a>
                </p>
                <p>@lang('Enrollment date'): {{ $enrollment->date }}</p>
                <p>@lang('Enrollment ID'): {{ $enrollment->id }}</p>
                <p>@lang('Course'): <a href="{{ route('course.show', ['id' => $enrollment->course_id]) }}">{{ $enrollment->course->name }}</a></p>
                <p>@lang('Period'): {{ $enrollment->course->period->name }}</p>

                <div class="form-group">
                    <a class="btn btn-sm btn-warning" href="{{ route('get-courses-list', ['mode' => 'update', 'enrollment_id' => $enrollment->id]) }}">@lang('Change course')</a>
                </div>

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
            </div>
        </div>
    </div>


    <div class="col-md-6">

        <student-comments
            :comments="{{ json_encode($enrollment->comments) }}"
            :id="{{ json_encode($enrollment->id) }}"
            :type="'App\\Models\\Enrollment'"
            route="{{ route('storeComment') }}">
        </student-comments>

    </div>

</div>

<div class="row">
    <div class="col-md-6">
        <div class="card">
            <div class="card-header">@lang('Invoicing')</div>

            <div class="card-body">

                <p><enrollment-price-field writeaccess="{{ $writeaccess }}" :enrollment="{{ json_encode($enrollment) }}" currency="{{ config('app.currency_symbol') }}" currencyposition="{{ config('app.currency_position') }}"></enrollment-price-field></p>

                <div class="form-group">

                    @if($enrollment->status_id == 2)

                        <div class="badge badge-primary">
                            {{ $enrollment->status }}
                        </div>

                    @elseif($enrollment->status_id == 1)
                        <div class="badge badge-warning">
                            {{ $enrollment->status }}
                        </div>
                </div>
                <div class="form-group">
                    @if(backpack_user()->can('enrollments.edit') && $enrollment->parent_id == null)
                        <a href="/enrollment/{{ $enrollment->id }}/bill" class="btn btn-primary">@lang('Checkout enrollment')</a>
                    @endif

                    @endif

                    @foreach ($enrollment->scholarships as $scholarship)
                        {{ $scholarship->name }} (<a href="#" onclick="if(confirm('Voulez-vous vraiment retirer cette bourse ?')) removeScholarship({{ $enrollment->id }}, {{ $scholarship->id }})">{{ __('Cancel') }}</a>)
                    @endforeach
                </div>

                @if(backpack_user()->can('enrollments.edit'))

                    <div class="form-group">
                        {{-- todo translate and improve the confirmation message --}}
                        <button class="btn btn-sm btn-danger" onclick="if(confirm('Voulez-vous vraiment supprimer cette inscription ?')) cancel({{ $enrollment->id }})">
                            @lang('Delete Enrollment')
                        </button>
                    </div>

                    <scholarship-modal-component enrollment_id="{{ $enrollment->id }}" :scholarships="{{ $scholarships }}"></scholarship-modal-component>

                    @if ($enrollment->invoices->count() > 0)<h3>@lang('Invoices')</h3>
                        <ul>
                            @foreach($enrollment->invoices as $invoice)
                                <li><a href="{{ route('invoice.show', ['id' => $invoice->id]) }}">{{ $invoice->invoice_reference }} - {{ $invoice->formatted_date }}</a></li>
                            @endforeach
                        </ul>
                    @endif
                @else
                    {{ $enrollment->status }}
                @endif

                @if(config('app.enrollment_sheet'))
                    <a href="{{ route('generate-enrollment-sheet', ['enrollment' => $enrollment]) }}">{{ __('Enrollment sheet') }} (.docx)</a>
                @endif
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
    function cancel(enrollment)
        {
            axios.delete(`/enrollment/${enrollment}`)
                .then(response => {
                    window.location.href="/enrollment"
                })
                .catch(error => console.log(error));
        }

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

@section('after_scripts')
    <script src="{{ mix('/js/app.js') }}"></script>
    <script src="{{ mix('/js/manifest.js') }}"></script>
    <script src="{{ mix('/js/vendor.js') }}"></script>
@endsection
