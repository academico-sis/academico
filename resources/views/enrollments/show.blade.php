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

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">@lang('Enrollment Info')
            </div>

            <div class="card-body">
                <p>@lang('name'):
                    <a href="{{ url('student') }}/{{$enrollment->student->id }}/show">{{ $enrollment->student->firstname }} {{ $enrollment->student->lastname }}</a>
                </p>
                <p>@lang('Enrollment date'): {{ $enrollment->date }}</p>
                <p>@lang('Enrollment ID'): {{ $enrollment->id }}</p>
                <p>@lang('Course'): <a href="{{ route('course.show', ['id' => $enrollment->course_id]) }}">{{ $enrollment->course->name }}</a></p>
                <enrollment-price-field writeaccess="{{ $writeaccess }}" :enrollment="{{ json_encode($enrollment) }}" currency="{{ config('app.currency_symbol') }}" currencyposition="{{ config('app.currency_position') }}"></enrollment-price-field>
                <p>@lang('Period'): {{ $enrollment->course->period->name }}</p>

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


    <div class="col-md-4">

        <student-comments
            :comments="{{ json_encode($enrollment->comments) }}"
            :id="{{ json_encode($enrollment->id) }}"
            :type="'App\\Models\\Enrollment'"
            route="{{ route('storeComment') }}">
        </student-comments>

    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header">@lang('Status')</div>

            <div class="card-body">

            <div class="form-group">

                @if($enrollment->status_id == 2)

                    <div class="label label-primary">
                        {{ $enrollment->enrollmentStatus->name }}
                    </div>

                @elseif($enrollment->status_id == 1)
                    <div class="label label-warning">
                        {{ $enrollment->enrollmentStatus->name }}
                    </div>

                    @if(backpack_user()->can('enrollments.edit') && $enrollment->parent_id == null && !$enrollment->invoice)

                        <div class="form-group">
                            <a href="/enrollment/{{ $enrollment->id }}/bill" class="btn btn-primary">@lang('Checkout enrollment')</a>
                        </div>

                    @endif

                @endif

                @foreach ($enrollment->scholarships as $scholarship)
                    {{ $scholarship->name }} (<a href="#" onclick="if(confirm('Voulez-vous vraiment retirer cette bourse ?')) removeScholarship({{ $enrollment->id }}, {{ $scholarship->id }})">{{ __('Cancel') }}</a>)
                @endforeach
            </div>

                @if(backpack_user()->can('enrollments.edit'))

                    <div class="form-group">
                        {{-- todo translate and improve the confirmation message --}}
                        <button class="btn btn-danger" onclick="if(confirm('Voulez-vous vraiment supprimer cette inscription ?')) cancel({{ $enrollment->id }})">
                            @lang('Delete Enrollment')
                        </button>
                    </div>

                <div class="form-group">
                    <enrollment-status-button :enrollment="{{json_encode($enrollment)}}"></enrollment-status-button>
                </div>

                <div class="form-group">
                    <a class="btn btn-sm btn-warning" href="{{ route('get-courses-list', ['mode' => 'update', 'enrollment_id' => $enrollment->id]) }}">@lang('Change course')</a>
                </div>

                <scholarship-modal-component enrollment_id="{{ $enrollment->id }}" :scholarships="{{ $scholarships }}"></scholarship-modal-component>

            @else
                {{ $enrollment->enrollmentStatus->name }}
            @endif

                <a href="{{ route('generate-enrollment-sheet', ['enrollment' => $enrollment]) }}">{{ __('Enrollment sheet') }} (.docx)</a>
            </div>
        </div>
    </div>
</div>

@if ($enrollment->invoice && backpack_user()->can('enrollments.edit'))
    @include('invoices.details', ['editable' => $enrollment->status_id !== 2, 'invoice' => $enrollment->invoice])
@endif

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
