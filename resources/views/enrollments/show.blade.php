@extends('backpack::blank')

@section('header')
<section class="container-fluid">
    <h2>
        @lang('Enrollment Details')
    </h2>
</section>
@endsection


@section('content')

<div class="row" id="app">

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

                @endif

            </div>

                @if(backpack_user()->can('enrollments.edit'))

                    @if($enrollment->status_id != 2)
                    <div class="form-group">
                        <button class="btn btn-info" onclick="if(confirm('Voulez-vous vraiment marquer cette inscription comme payée ?')) markaspaid({{ $enrollment->id }})">
                            @lang('Mark as paid')
                        </button>
                    </div>
                    @endif

                    {{-- todo translate and improve the confirmation message --}}
                    <div class="form-group">

                        <button class="btn btn-danger" onclick="if(confirm('Voulez-vous vraiment supprimer cette inscription ?')) cancel({{ $enrollment->id }})">
                            @lang('Delete Enrollment')
                        </button>

                    </div>

                <div class="form-group">
                    <a class="btn btn-sm btn-warning" href="{{ route('get-courses-list', ['mode' => 'update', 'enrollment_id' => $enrollment->id]) }}">@lang('Change course')</a>
                </div>

            @else
                {{ $enrollment->enrollmentStatus->name }}
            @endif

            </div>
        </div>
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
                .catch(error => {
                    console.log(error);
                });
        }


        function markaspaid(enrollment)
        {
            axios.post(`/enrollment/${enrollment}/markaspaid`)
                .then(response => {
                    window.location.reload()
                })
                .catch(error => {
                    console.log(error);
                });
        }
</script>

@endsection

@section('after_scripts')
    <script src="/js/app.js"></script>
@endsection
